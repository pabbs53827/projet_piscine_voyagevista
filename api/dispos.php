<?php
declare(strict_types=1);

// http://localhost/voyagevista/api/dispos.php?hebergement=1  (GET)
require __DIR__ . '/_helpers.php';
require __DIR__ . '/_db.php';

if ($_SERVER['REQUEST_METHOD'] !== 'GET') json_error('Méthode non autorisée.', 405);

$idHeb = isset($_GET['hebergement']) ? (int)$_GET['hebergement'] : 0;
if ($idHeb <= 0) json_error('Identifiant manquant.');

$stmt = db()->prepare(
    'SELECT * FROM disponibilite
     WHERE id_hebergement = ? AND statut = "ouverte" AND date_debut >= CURDATE()
     ORDER BY date_debut'
);
$stmt->execute([$idHeb]);
json_response(['dispos' => $stmt->fetchAll()]);
