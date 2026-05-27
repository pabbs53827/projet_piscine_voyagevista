<?php
declare(strict_types=1);

// POST { id_sejour, id_hebergement, date_debut, date_fin }
require __DIR__ . '/_helpers.php';
require __DIR__ . '/_db.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') json_error('Méthode non autorisée.', 405);

$user = require_auth();
$body = read_json_body();

$idSejour  = isset($body['id_sejour'])      ? (int)$body['id_sejour']        : 0;
$idHeb     = isset($body['id_hebergement']) ? (int)$body['id_hebergement']   : 0;
$dateDebut = isset($body['date_debut'])     ? trim($body['date_debut'])       : '';
$dateFin   = isset($body['date_fin'])       ? trim($body['date_fin'])         : '';

if ($idSejour <= 0 || $idHeb <= 0 || !$dateDebut || !$dateFin) {
    json_error('Données manquantes.');
}

// Validation des dates
$dDebut = DateTimeImmutable::createFromFormat('Y-m-d', $dateDebut);
$dFin   = DateTimeImmutable::createFromFormat('Y-m-d', $dateFin);
if (!$dDebut || !$dFin) json_error('Format de date invalide.');
if ($dFin <= $dDebut)   json_error('La date de fin doit être après la date de début.');
if ($dDebut < new DateTimeImmutable('today')) json_error('La date de début ne peut pas être dans le passé.');

$pdo = db();

// Vérifie que le séjour appartient à l'utilisateur
$stmt = $pdo->prepare('SELECT id_sejour FROM sejour WHERE id_sejour = ? AND id_voyageur = ? AND statut = "brouillon"');
$stmt->execute([$idSejour, $user['id']]);
if (!$stmt->fetch()) json_error('Séjour introuvable.', 404);

$pdo->beginTransaction();

try {
    // Verrou sur l'hébergement pour éviter les réservations simultanées
    $stmt = $pdo->prepare('SELECT prix_semaine FROM hebergement WHERE id_hebergement = ? FOR UPDATE');
    $stmt->execute([$idHeb]);
    $heb = $stmt->fetch();
    if (!$heb) { $pdo->rollBack(); json_error('Hébergement introuvable.', 404); }

    // Détection de chevauchement avec des réservations existantes
    // Deux intervalles [A,B] et [C,D] se chevauchent si A < D ET B > C
    $stmt = $pdo->prepare(
        'SELECT COUNT(*) FROM disponibilite
         WHERE id_hebergement = ?
           AND statut = "reservee"
           AND date_debut < ?
           AND date_fin   > ?'
    );
    $stmt->execute([$idHeb, $dateFin, $dateDebut]);
    if ((int)$stmt->fetchColumn() > 0) {
        $pdo->rollBack();
        json_error('Ces dates sont déjà réservées pour cet hébergement.', 409);
    }

    // Calcul du prix (arrondi à la semaine supérieure)
    $jours    = (int)$dDebut->diff($dFin)->days;
    $semaines = max(1, (int)ceil($jours / 7));
    $prix     = round($semaines * (float)$heb['prix_semaine'], 2);

    // Création de la disponibilité à la volée
    $pdo->prepare(
        'INSERT INTO disponibilite (id_hebergement, date_debut, date_fin, statut) VALUES (?, ?, ?, "reservee")'
    )->execute([$idHeb, $dateDebut, $dateFin]);
    $idDispo = (int)$pdo->lastInsertId();

    // Ajout au séjour
    $pdo->prepare('INSERT INTO sejour_hebergement (id_sejour, id_dispo, prix) VALUES (?, ?, ?)')->execute([$idSejour, $idDispo, $prix]);

    // Mise à jour du total
    $pdo->prepare('UPDATE sejour SET prix_total = prix_total + ? WHERE id_sejour = ?')->execute([$prix, $idSejour]);

    $pdo->commit();
    json_response(['message' => 'Hébergement ajouté à votre séjour.', 'prix' => $prix]);
} catch (Throwable $e) {
    $pdo->rollBack();
    throw $e;
}
