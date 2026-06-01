<?php
declare(strict_types=1);
require __DIR__ . '/_helpers.php';
require __DIR__ . '/_db.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') json_error('Méthode non autorisée.', 405);
$user = require_role('hote', 'admin');
$body = read_json_body();

$id_heb    = isset($body['id_hebergement']) ? (int)$body['id_hebergement'] : 0;
$date_debut = trim((string)($body['date_debut'] ?? ''));
$date_fin   = trim((string)($body['date_fin']   ?? ''));

if ($id_heb <= 0)      json_error('Hébergement invalide.');
if ($date_debut === '') json_error('Date de début obligatoire.');
if ($date_fin === '')   json_error('Date de fin obligatoire.');

$d1 = DateTime::createFromFormat('Y-m-d', $date_debut);
$d2 = DateTime::createFromFormat('Y-m-d', $date_fin);
if (!$d1 || !$d2)           json_error('Format de date invalide (YYYY-MM-DD).');
if ($d2 <= $d1)             json_error('La date de fin doit être après la date de début.');
if ($d1 < new DateTime('today')) json_error('La date de début ne peut pas être dans le passé.');

$pdo  = db();
// Vérification : l'hébergement appartient bien à cet hôte.
$stmt = $pdo->prepare('SELECT 1 FROM hebergement WHERE id_hebergement = ? AND id_hote = ?');
$stmt->execute([$id_heb, $user['id']]);
if (!$stmt->fetch()) json_error('Hébergement introuvable ou accès refusé.', 403);

$pdo->prepare(
    'INSERT INTO disponibilite (id_hebergement, date_debut, date_fin, statut) VALUES (?, ?, ?, "ouverte")'
)->execute([$id_heb, $date_debut, $date_fin]);

json_response(['message' => 'Disponibilité ajoutée.'], 201);
