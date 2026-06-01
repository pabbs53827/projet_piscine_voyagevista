<?php
declare(strict_types=1);

// POST { id_sejour, id_creneau, nb_participants }
require __DIR__ . '/_helpers.php';
require __DIR__ . '/_db.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') json_error('Méthode non autorisée.', 405);

$user = require_auth();
$body = read_json_body();

$idSejour  = isset($body['id_sejour'])       ? (int)$body['id_sejour']      : 0;
$idCreneau = isset($body['id_creneau'])      ? (int)$body['id_creneau']     : 0;
$nbPart    = isset($body['nb_participants']) ? (int)$body['nb_participants'] : 1;

if ($idSejour <= 0 || $idCreneau <= 0 || $nbPart < 1) json_error('Données manquantes ou invalides.');

$pdo = db();

$stmt = $pdo->prepare('SELECT id_sejour FROM sejour WHERE id_sejour = ? AND id_voyageur = ? AND statut = "brouillon"');
$stmt->execute([$idSejour, $user['id']]);
if (!$stmt->fetch()) json_error('Séjour introuvable.', 404);

$pdo->beginTransaction();

try {
    // Verrou sur le créneau (même logique que pour l'hébergement).
    $stmt = $pdo->prepare(
        'SELECT c.*, a.prix_personne
         FROM creneau c
         JOIN activite a ON a.id_activite = c.id_activite
         WHERE c.id_creneau = ? AND c.places_restantes >= ?
         FOR UPDATE'
    );
    $stmt->execute([$idCreneau, $nbPart]);
    $creneau = $stmt->fetch();

    if (!$creneau) {
        $pdo->rollBack();
        json_error('Plus assez de places disponibles sur ce créneau.', 409);
    }

    $prix = round($nbPart * (float)$creneau['prix_personne'], 2);

    $pdo->prepare('INSERT INTO sejour_activite (id_sejour, id_creneau, nb_participants, prix) VALUES (?, ?, ?, ?)')->execute([$idSejour, $idCreneau, $nbPart, $prix]);
    $pdo->prepare('UPDATE creneau SET places_restantes = places_restantes - ? WHERE id_creneau = ?')->execute([$nbPart, $idCreneau]);
    $pdo->prepare('UPDATE sejour SET prix_total = prix_total + ? WHERE id_sejour = ?')->execute([$prix, $idSejour]);

    $pdo->commit();
    json_response(['message' => 'Activité ajoutée à votre séjour.', 'prix' => $prix]);
} catch (Throwable $e) {
    $pdo->rollBack();
    throw $e;
}
