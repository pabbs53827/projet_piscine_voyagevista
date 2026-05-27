<?php
declare(strict_types=1);

// POST { id_sejour, id_dispo }
require __DIR__ . '/_helpers.php';
require __DIR__ . '/_db.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') json_error('Méthode non autorisée.', 405);

$user = require_auth();
$body = read_json_body();

$idSejour = isset($body['id_sejour']) ? (int)$body['id_sejour'] : 0;
$idDispo  = isset($body['id_dispo'])  ? (int)$body['id_dispo']  : 0;
if ($idSejour <= 0 || $idDispo <= 0) json_error('Données manquantes.');

$pdo = db();

// Vérification : le séjour appartient bien à l'utilisateur connecté.
$stmt = $pdo->prepare('SELECT id_sejour FROM sejour WHERE id_sejour = ? AND id_voyageur = ? AND statut = "brouillon"');
$stmt->execute([$idSejour, $user['id']]);
if (!$stmt->fetch()) json_error('Séjour introuvable.', 404);

// -------------------------------------------------------
// GESTION DE LA CONCURRENCE (point clé du sujet)
//
// SELECT ... FOR UPDATE verrouille la ligne dans la transaction.
// Si deux requêtes arrivent simultanément pour la même disponibilité,
// la première pose le verrou, insère, met à jour, puis libère.
// La seconde lit alors statut = "reservee" → 409, sans double réservation.
// -------------------------------------------------------
$pdo->beginTransaction();

try {
    $stmt = $pdo->prepare(
        'SELECT dp.*, h.prix_semaine
         FROM disponibilite dp
         JOIN hebergement h ON h.id_hebergement = dp.id_hebergement
         WHERE dp.id_dispo = ? AND dp.statut = "ouverte"
         FOR UPDATE'
    );
    $stmt->execute([$idDispo]);
    $dispo = $stmt->fetch();

    if (!$dispo) {
        $pdo->rollBack();
        json_error('Cette période n\'est plus disponible — quelqu\'un vient de la réserver.', 409);
    }

    // Calcul du prix proportionnel à la durée (arrondi à la semaine supérieure).
    $debut   = new DateTime($dispo['date_debut']);
    $fin     = new DateTime($dispo['date_fin']);
    $jours   = (int)$debut->diff($fin)->days;
    $semaines = max(1, (int)ceil($jours / 7));
    $prix    = round($semaines * (float)$dispo['prix_semaine'], 2);

    // Ajout au séjour.
    $pdo->prepare('INSERT INTO sejour_hebergement (id_sejour, id_dispo, prix) VALUES (?, ?, ?)')->execute([$idSejour, $idDispo, $prix]);

    // La disponibilité est désormais prise.
    $pdo->prepare('UPDATE disponibilite SET statut = "reservee" WHERE id_dispo = ?')->execute([$idDispo]);

    // Mise à jour du total du séjour.
    $pdo->prepare('UPDATE sejour SET prix_total = prix_total + ? WHERE id_sejour = ?')->execute([$prix, $idSejour]);

    $pdo->commit();
    json_response(['message' => 'Hébergement ajouté à votre séjour.', 'prix' => $prix]);
} catch (Throwable $e) {
    $pdo->rollBack();
    throw $e;
}
