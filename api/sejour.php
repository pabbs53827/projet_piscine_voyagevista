<?php
declare(strict_types=1);

// GET  : retourne le séjour brouillon en cours (ou null)
// POST : retourne le brouillon existant, ou en crée un nouveau
require __DIR__ . '/_helpers.php';
require __DIR__ . '/_db.php';

$user = require_auth();
$pdo  = db();

$stmt = $pdo->prepare(
    'SELECT * FROM sejour WHERE id_voyageur = ? AND statut = "brouillon"
     ORDER BY date_creation DESC LIMIT 1'
);
$stmt->execute([$user['id']]);
$sejour = $stmt->fetch();

if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    json_response(['sejour' => $sejour ?: null]);
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!$sejour) {
        $body  = read_json_body();
        $titre = trim((string)($body['titre'] ?? 'Mon séjour'));
        $titre = $titre !== '' ? $titre : 'Mon séjour';

        $pdo->prepare('INSERT INTO sejour (id_voyageur, titre) VALUES (?, ?)')->execute([$user['id'], $titre]);
        $id   = (int)$pdo->lastInsertId();
        $stmt = $pdo->prepare('SELECT * FROM sejour WHERE id_sejour = ?');
        $stmt->execute([$id]);
        $sejour = $stmt->fetch();
    }
    json_response(['sejour' => $sejour]);
}

json_error('Méthode non autorisée.', 405);
