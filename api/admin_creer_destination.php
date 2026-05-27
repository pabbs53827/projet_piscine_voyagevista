<?php
declare(strict_types=1);
require __DIR__ . '/_helpers.php';
require __DIR__ . '/_db.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') json_error('Méthode non autorisée.', 405);
$user = require_role('admin');
$body = read_json_body();

$nom         = trim((string)($body['nom']         ?? ''));
$pays        = trim((string)($body['pays']        ?? ''));
$region      = trim((string)($body['region']      ?? ''));
$description = trim((string)($body['description'] ?? ''));
$latitude    = isset($body['latitude'])  ? (float)$body['latitude']  : null;
$longitude   = isset($body['longitude']) ? (float)$body['longitude'] : null;

if ($nom === '')  json_error('Le nom est obligatoire.');
if ($pays === '') json_error('Le pays est obligatoire.');

db()->prepare(
    'INSERT INTO destination (id_admin, nom, pays, region, latitude, longitude, description)
     VALUES (?, ?, ?, ?, ?, ?, ?)'
)->execute([$user['id'], $nom, $pays, $region ?: null, $latitude, $longitude, $description ?: null]);

json_response(['message' => 'Destination publiée.', 'id' => (int)db()->lastInsertId()], 201);
