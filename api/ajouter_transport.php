<?php
declare(strict_types=1);

// POST { id_sejour, id_transport, nb_passagers }
require __DIR__ . '/_helpers.php';
require __DIR__ . '/_db.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') json_error('Méthode non autorisée.', 405);

$user = require_auth();
$body = read_json_body();

$idSejour   = isset($body['id_sejour'])    ? (int)$body['id_sejour']    : 0;
$idTransport = isset($body['id_transport']) ? (int)$body['id_transport'] : 0;
$nbPass      = isset($body['nb_passagers']) ? (int)$body['nb_passagers'] : 1;

if ($idSejour <= 0 || $idTransport <= 0 || $nbPass < 1) json_error('Données manquantes ou invalides.');

$pdo = db();

// Le séjour appartient bien à l'utilisateur.
$stmt = $pdo->prepare('SELECT id_sejour FROM sejour WHERE id_sejour = ? AND id_voyageur = ? AND statut = "brouillon"');
$stmt->execute([$idSejour, $user['id']]);
if (!$stmt->fetch()) json_error('Séjour introuvable.', 404);

$pdo->beginTransaction();
try {
    // Verrou sur le transport — même logique que l'hébergement.
    $stmt = $pdo->prepare(
        'SELECT * FROM transport WHERE id_transport = ? AND places_dispo >= ? FOR UPDATE'
    );
    $stmt->execute([$idTransport, $nbPass]);
    $transport = $stmt->fetch();

    if (!$transport) {
        $pdo->rollBack();
        json_error('Plus assez de places disponibles sur ce transport.', 409);
    }

    $prix = round($nbPass * (float)$transport['prix'], 2);

    $pdo->prepare('INSERT INTO sejour_transport (id_sejour, id_transport, nb_passagers, prix) VALUES (?, ?, ?, ?)')->execute([$idSejour, $idTransport, $nbPass, $prix]);
    $pdo->prepare('UPDATE transport SET places_dispo = places_dispo - ? WHERE id_transport = ?')->execute([$nbPass, $idTransport]);
    $pdo->prepare('UPDATE sejour SET prix_total = prix_total + ? WHERE id_sejour = ?')->execute([$prix, $idSejour]);

    $pdo->commit();
    json_response(['message' => 'Transport ajouté à votre séjour.', 'prix' => $prix]);
} catch (Throwable $e) {
    $pdo->rollBack();
    throw $e;
}
