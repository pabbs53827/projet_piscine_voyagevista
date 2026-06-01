<?php
declare(strict_types=1);

// http://localhost/voyagevista/api/activites.php?destination=1&categorie=nature  (GET)
require __DIR__ . '/_helpers.php';
require __DIR__ . '/_db.php';

if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
    json_error('Méthode non autorisée.', 405); 
}

$idDest    = isset($_GET['destination']) ? (int)$_GET['destination']    : 0;
$categorie = trim((string)($_GET['categorie'] ?? ''));
$niveau    = trim((string)($_GET['niveau']    ?? ''));

$sql    = 'SELECT a.*, d.nom AS destination
           FROM activite a
           JOIN destination d ON d.id_destination = a.id_destination
           WHERE 1 = 1';
$params = [];

if ($idDest > 0) {
    $sql .= ' AND a.id_destination = ?';
    $params[] = $idDest;
}
if ($categorie !== '') {
    $sql .= ' AND a.categorie = ?';
    $params[] = $categorie;
}
if ($niveau !== '') {
    $sql .= ' AND a.niveau = ?';
    $params[] = $niveau;
}
$sql .= ' ORDER BY a.prix_personne';

$stmt = db()->prepare($sql);
$stmt->execute($params);

json_response(['activites' => $stmt->fetchAll()]);
