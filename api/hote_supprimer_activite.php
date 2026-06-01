<?php
declare(strict_types=1);
require __DIR__ . '/_helpers.php';
require __DIR__ . '/_db.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') json_error('Méthode non autorisée.', 405);
$user = require_role('hote', 'admin');
$body = read_json_body();
$id   = (int)($body['id'] ?? 0);
if ($id <= 0) json_error('Identifiant manquant.');

$pdo  = db();
$stmt = $pdo->prepare('SELECT id_activite FROM activite WHERE id_activite = ? AND id_hote = ?');
$stmt->execute([$id, $user['id']]);
if (!$stmt->fetch()) json_error('Activité introuvable.', 404);

$pdo->prepare('DELETE FROM activite WHERE id_activite = ?')->execute([$id]);
json_response(['message' => 'Activité supprimée.']);
