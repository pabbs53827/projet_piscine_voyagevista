<?php
declare(strict_types=1);
require __DIR__ . '/_helpers.php';
require __DIR__ . '/_db.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') json_error('Méthode non autorisée.', 405);
$user = require_role('hote', 'admin');
$body = read_json_body();

$nom          = trim((string)($body['nom']          ?? ''));
$description  = trim((string)($body['description']  ?? ''));
$categorie    = (string)($body['categorie']   ?? '');
$prix         = isset($body['prix_personne']) ? (float)$body['prix_personne'] : 0;
$duree        = isset($body['duree_h'])       ? (float)$body['duree_h']       : 1;
$niveau       = (string)($body['niveau']      ?? 'facile');
$id_dest      = isset($body['id_destination']) ? (int)$body['id_destination'] : 0;
$date_heure   = trim((string)($body['date_heure']    ?? ''));
$places       = isset($body['places_totales']) ? (int)$body['places_totales'] : 1;

if ($nom === '')  json_error('Le nom est obligatoire.');
if (!in_array($categorie, ['aventure','culture','nature','gastronomie','bien_etre'], true)) json_error('Catégorie invalide.');
if ($prix <= 0)   json_error('Le prix doit être positif.');
if ($id_dest <= 0) json_error('Destination invalide.');
if ($date_heure === '') json_error('Date et heure du premier créneau obligatoires.');
if ($places < 1)  json_error('Le nombre de places doit être au moins 1.');
if (!in_array($niveau, ['facile','moyen','difficile'], true)) json_error('Niveau invalide.');

$dt = DateTime::createFromFormat('Y-m-d\TH:i', $date_heure)
   ?: DateTime::createFromFormat('Y-m-d H:i', $date_heure)
   ?: DateTime::createFromFormat('Y-m-d H:i:s', $date_heure);
if (!$dt) json_error('Format de date invalide.');
$date_heure_sql = $dt->format('Y-m-d H:i:s');

$pdo = db();
$stmt = $pdo->prepare('SELECT 1 FROM destination WHERE id_destination = ?');
$stmt->execute([$id_dest]);
if (!$stmt->fetch()) json_error('Destination introuvable.', 404);

$pdo->beginTransaction();
$pdo->prepare(
    'INSERT INTO activite (id_destination, id_hote, nom, description, categorie, prix_personne, duree_h, niveau)
     VALUES (?, ?, ?, ?, ?, ?, ?, ?)'
)->execute([$id_dest, $user['id'], $nom, $description, $categorie, $prix, $duree, $niveau]);
$id_activite = (int)$pdo->lastInsertId();

$pdo->prepare(
    'INSERT INTO creneau (id_activite, date_heure, places_totales, places_restantes) VALUES (?, ?, ?, ?)'
)->execute([$id_activite, $date_heure_sql, $places, $places]);
$pdo->commit();

json_response(['message' => 'Activité publiée.', 'id' => $id_activite], 201);
