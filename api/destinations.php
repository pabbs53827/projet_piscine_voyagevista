<?php
declare(strict_types=1);

// http://localhost/voyagevista/api/destinations.php?q=kyoto&pays=Japon  (GET)
require __DIR__ . '/_helpers.php';
require __DIR__ . '/_db.php';

if ($_SERVER['REQUEST_METHOD'] !== 'GET') json_error('Méthode non autorisée.', 405);

$q    = trim((string)($_GET['q']    ?? ''));
$pays = trim((string)($_GET['pays'] ?? ''));
$sort = trim((string)($_GET['sort'] ?? 'nom'));

$sql = 'SELECT d.*,
    (SELECT MIN(h.prix_semaine) FROM hebergement h WHERE h.id_destination = d.id_destination) AS prix_min,
    (SELECT COUNT(*)            FROM hebergement h WHERE h.id_destination = d.id_destination) AS nb_hebergements
  FROM destination d WHERE 1 = 1';
$params = [];

if ($q !== '') {
    $sql .= ' AND (d.nom LIKE ? OR d.pays LIKE ? OR d.region LIKE ?)';
    $like = "%$q%";
    array_push($params, $like, $like, $like);
}
if ($pays !== '') {
    $sql .= ' AND d.pays = ?';
    $params[] = $pays;
}
$sql .= match($sort) {
    'prix_asc'  => ' ORDER BY prix_min ASC',
    'prix_desc' => ' ORDER BY prix_min DESC',
    default     => ' ORDER BY d.nom',
};

// Retourner aussi la liste des pays pour le filtre
$paysList = db()->query('SELECT DISTINCT pays FROM destination ORDER BY pays')->fetchAll(PDO::FETCH_COLUMN);

$stmt = db()->prepare($sql);
$stmt->execute($params);
json_response(['destinations' => $stmt->fetchAll(), 'pays' => $paysList]);
