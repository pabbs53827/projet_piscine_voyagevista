<?php
declare(strict_types=1);
require __DIR__ . '/_helpers.php';
require __DIR__ . '/_db.php';

if ($_SERVER['REQUEST_METHOD'] !== 'GET') json_error('Méthode non autorisée.', 405);
$user = require_role('hote', 'admin');
$pdo  = db();

$stmt = $pdo->prepare(
    'SELECT h.*, d.nom AS destination,
       (SELECT COUNT(*) FROM disponibilite dp
        WHERE dp.id_hebergement = h.id_hebergement AND dp.statut = "ouverte"
          AND dp.date_debut >= CURDATE()) AS nb_dispos
     FROM hebergement h
     JOIN destination d ON d.id_destination = h.id_destination
     WHERE h.id_hote = ?
     ORDER BY h.id_hebergement DESC'
);
$stmt->execute([$user['id']]);
$hebergements = $stmt->fetchAll();

$stmt = $pdo->prepare(
    'SELECT a.*, d.nom AS destination,
       (SELECT COUNT(*) FROM creneau c
        WHERE c.id_activite = a.id_activite
          AND c.places_restantes > 0 AND c.date_heure >= NOW()) AS nb_creneaux
     FROM activite a
     JOIN destination d ON d.id_destination = a.id_destination
     WHERE a.id_hote = ?
     ORDER BY a.id_activite DESC'
);
$stmt->execute([$user['id']]);
$activites = $stmt->fetchAll();

json_response(['hebergements' => $hebergements, 'activites' => $activites]);
