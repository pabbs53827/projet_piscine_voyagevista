<?php
declare(strict_types=1);

// http://localhost/voyagevista/api/transports.php?destination=1  (GET)
require __DIR__ . '/_helpers.php';
require __DIR__ . '/_db.php';

if ($_SERVER['REQUEST_METHOD'] !== 'GET') json_error('Méthode non autorisée.', 405);

$idDest = isset($_GET['destination']) ? (int)$_GET['destination'] : 0;
if ($idDest <= 0) json_error('Identifiant manquant.');

$stmt = db()->prepare(
    'SELECT * FROM transport
     WHERE id_destination_arr = ?
       AND date_depart >= NOW()
       AND places_dispo > 0
     ORDER BY date_depart'
);
$stmt->execute([$idDest]);
json_response(['transports' => $stmt->fetchAll()]);
