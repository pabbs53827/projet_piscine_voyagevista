<?php
declare(strict_types=1);

// http://localhost/voyagevista/api/hebergements.php?destination=1&prix_max=500&capacite_min=2  (GET)
require __DIR__ . '/_helpers.php';
require __DIR__ . '/_db.php';

if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
    json_error('Méthode non autorisée.', 405);
}

$idDest  = isset($_GET['destination'])  ? (int)$_GET['destination']    : 0;
$prixMax = isset($_GET['prix_max'])     ? (float)$_GET['prix_max']     : 0;
$capaMin = isset($_GET['capacite_min']) ? (int)$_GET['capacite_min']   : 0;

$sql = 'SELECT h.id_hebergement, h.nom, h.type, h.description, h.capacite,
               h.prix_semaine, h.duree_min_nuits, h.image_url,
               d.id_destination, d.nom AS destination
        FROM hebergement h
        JOIN destination d ON d.id_destination = h.id_destination
        WHERE 1 = 1';
$params = [];

if ($idDest > 0) {
    $sql .= ' AND h.id_destination = ?';
    $params[] = $idDest;
}
if ($prixMax > 0) {
    $sql .= ' AND h.prix_semaine <= ?';
    $params[] = $prixMax;
}
if ($capaMin > 0) {
    $sql .= ' AND h.capacite >= ?';
    $params[] = $capaMin;
}
$sql .= ' ORDER BY h.prix_semaine';

$stmt = db()->prepare($sql);
$stmt->execute($params);

json_response(['hebergements' => $stmt->fetchAll()]);
