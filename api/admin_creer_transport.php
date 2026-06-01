<?php
declare(strict_types=1);
require __DIR__ . '/_helpers.php';
require __DIR__ . '/_db.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') json_error('Méthode non autorisée.', 405);
require_role('admin');
$body = read_json_body();

$id_dest    = (int)($body['id_destination'] ?? 0);
$depart     = trim((string)($body['ville_depart'] ?? ''));
$type       = (string)($body['type']        ?? '');
$compagnie  = trim((string)($body['compagnie']   ?? ''));
$date_dep   = trim((string)($body['date_depart'] ?? ''));
$date_arr   = trim((string)($body['date_arrivee'] ?? ''));
$prix       = (float)($body['prix']         ?? 0);
$places     = (int)($body['places_dispo']   ?? 0);

if ($id_dest <= 0)   json_error('Destination manquante.');
if ($depart === '')  json_error('Ville de départ manquante.');
if (!in_array($type, ['avion','train','bus','ferry'], true)) json_error('Type de transport invalide.');
if ($date_dep === '' || $date_arr === '') json_error('Dates manquantes.');
if ($prix < 0)       json_error('Prix invalide.');
if ($places <= 0)    json_error('Nombre de places invalide.');

// Validation dates : arrivée doit être après le départ
if (strtotime($date_arr) <= strtotime($date_dep)) {
    json_error('La date d\'arrivée doit être postérieure à la date de départ.');
}

$pdo = db();
$stmt = $pdo->prepare('SELECT id_destination FROM destination WHERE id_destination = ?');
$stmt->execute([$id_dest]);
if (!$stmt->fetch()) json_error('Destination introuvable.', 404);

$pdo->prepare(
    'INSERT INTO transport (id_destination_arr,ville_depart,type,compagnie,date_depart,date_arrivee,prix,places_dispo)
     VALUES (?,?,?,?,?,?,?,?)'
)->execute([$id_dest, $depart, $type, $compagnie, $date_dep, $date_arr, $prix, $places]);

json_response(['message' => 'Transport créé.'], 201);
