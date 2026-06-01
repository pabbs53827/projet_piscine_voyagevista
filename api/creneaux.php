<?php
declare(strict_types=1);

// http://localhost/voyagevista/api/creneaux.php?activite=1  (GET)
require __DIR__ . '/_helpers.php';
require __DIR__ . '/_db.php';

if ($_SERVER['REQUEST_METHOD'] !== 'GET') json_error('Méthode non autorisée.', 405);

$idAct = isset($_GET['activite']) ? (int)$_GET['activite'] : 0;
if ($idAct <= 0) json_error('Identifiant manquant.');

$stmt = db()->prepare(
    'SELECT * FROM creneau
     WHERE id_activite = ? AND date_heure >= NOW() AND places_restantes > 0
     ORDER BY date_heure'
);
$stmt->execute([$idAct]);
json_response(['creneaux' => $stmt->fetchAll()]);
