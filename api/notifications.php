<?php
declare(strict_types=1);
require __DIR__ . '/_helpers.php';
require __DIR__ . '/_db.php';

$user = require_auth();
$pdo  = db();

if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    $stmt = $pdo->prepare(
        'SELECT * FROM notification WHERE id_utilisateur = ?
         ORDER BY date_creation DESC LIMIT 30'
    );
    $stmt->execute([$user['id']]);
    $notifs = $stmt->fetchAll();

    $stmt2 = $pdo->prepare('SELECT COUNT(*) FROM notification WHERE id_utilisateur = ? AND lu = 0');
    $stmt2->execute([$user['id']]);
    $nb_non_lues = (int)$stmt2->fetchColumn();

    json_response(['notifications' => $notifs, 'nb_non_lues' => $nb_non_lues]);
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $pdo->prepare('UPDATE notification SET lu = 1 WHERE id_utilisateur = ?')->execute([$user['id']]);
    json_response(['message' => 'Notifications marquées comme lues.']);
}

json_error('Méthode non autorisée.', 405);
