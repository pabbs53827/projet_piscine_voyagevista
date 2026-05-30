<?php
declare(strict_types=1); 
require __DIR__ . '/_helpers.php';
require __DIR__ . '/_db.php';

$user = require_auth();
$pdo  = db();

if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    $stmt = $pdo->prepare('SELECT id_destination FROM favori WHERE id_utilisateur = ?');
    $stmt->execute([$user['id']]);
    json_response(['favoris' => $stmt->fetchAll(PDO::FETCH_COLUMN)]);
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $body = read_json_body();
    $idDest = (int)($body['id_destination'] ?? 0);
    if ($idDest <= 0) json_error('Identifiant manquant.');

    // Toggle
    $stmt = $pdo->prepare('SELECT id_favori FROM favori WHERE id_utilisateur = ? AND id_destination = ?');
    $stmt->execute([$user['id'], $idDest]);
    if ($stmt->fetch()) {
        $pdo->prepare('DELETE FROM favori WHERE id_utilisateur = ? AND id_destination = ?')
            ->execute([$user['id'], $idDest]);
        json_response(['action' => 'removed']);
    } else {
        $pdo->prepare('INSERT INTO favori (id_utilisateur, id_destination) VALUES (?, ?)')
            ->execute([$user['id'], $idDest]);
        json_response(['action' => 'added']);
    }
}

json_error('Méthode non autorisée.', 405);
