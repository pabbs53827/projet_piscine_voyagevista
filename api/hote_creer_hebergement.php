<?php
declare(strict_types=1);
require __DIR__ . '/_helpers.php';
require __DIR__ . '/_db.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') json_error('Méthode non autorisée.', 405);
$user = require_role('hote', 'admin');
$body = read_json_body();

$nom           = trim((string)($body['nom']            ?? ''));
$type          = (string)($body['type']          ?? '');
$capacite      = isset($body['capacite'])      ? (int)$body['capacite']      : 0;
$prix_semaine  = isset($body['prix_semaine'])  ? (float)$body['prix_semaine']  : 0;
$duree_min     = isset($body['duree_min_nuits']) ? (int)$body['duree_min_nuits'] : 7;
$id_dest       = isset($body['id_destination']) ? (int)$body['id_destination']  : 0;

if ($nom === '')                    json_error('Le nom est obligatoire.');
if (!in_array($type, ['appartement','maison','villa','chambre','autre'], true)) json_error('Type invalide.');
if ($capacite < 1)                  json_error('Capacité invalide.');
if ($prix_semaine <= 0)             json_error('Le prix doit être positif.');
if ($duree_min < 1)                 json_error('Durée minimum invalide.');
if ($id_dest <= 0)                  json_error('Destination invalide.');

$pdo  = db();
$stmt = $pdo->prepare('SELECT 1 FROM destination WHERE id_destination = ?');
$stmt->execute([$id_dest]);
if (!$stmt->fetch()) json_error('Destination introuvable.', 404);

$pdo->prepare(
    'INSERT INTO hebergement (id_destination, id_hote, nom, type, capacite, prix_semaine, duree_min_nuits)
     VALUES (?, ?, ?, ?, ?, ?, ?)'
)->execute([$id_dest, $user['id'], $nom, $type, $capacite, $prix_semaine, $duree_min]);

json_response(['message' => 'Hébergement publié.', 'id' => (int)$pdo->lastInsertId()], 201);
