<?php
declare(strict_types=1);

// http://localhost/voyagevista/api/destination.php?id=1  (GET)
require __DIR__ . '/_helpers.php';
require __DIR__ . '/_db.php';

if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
    json_error('Méthode non autorisée.', 405);
}

$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
if ($id <= 0) {
    json_error('Identifiant manquant.', 400);
}

$pdo = db();

// Destination
$stmt = $pdo->prepare('SELECT * FROM destination WHERE id_destination = ?');
$stmt->execute([$id]);
$destination = $stmt->fetch();
if (!$destination) {
    json_error('Destination introuvable.', 404);
}

// Hébergements avec leur prochaine disponibilité ouverte.
// La sous-requête évite un JOIN qui multiplierait les lignes.
$stmt = $pdo->prepare(
    'SELECT h.*,
       (SELECT MIN(dp.date_debut)
        FROM disponibilite dp
        WHERE dp.id_hebergement = h.id_hebergement
          AND dp.statut = "ouverte"
          AND dp.date_debut >= CURDATE()
       ) AS prochaine_dispo
     FROM hebergement h
     WHERE h.id_destination = ?
     ORDER BY h.prix_semaine'
);
$stmt->execute([$id]);
$hebergements = $stmt->fetchAll();

// Activités avec leur prochain créneau disponible.
$stmt = $pdo->prepare(
    'SELECT a.*,
       (SELECT MIN(c.date_heure)
        FROM creneau c
        WHERE c.id_activite = a.id_activite
          AND c.date_heure >= NOW()
          AND c.places_restantes > 0
       ) AS prochain_creneau
     FROM activite a
     WHERE a.id_destination = ?
     ORDER BY a.prix_personne'
);
$stmt->execute([$id]);
$activites = $stmt->fetchAll();

// Transports disponibles vers cette destination.
$stmt = $pdo->prepare(
    'SELECT * FROM transport
     WHERE id_destination_arr = ?
       AND date_depart >= NOW()
       AND places_dispo > 0
     ORDER BY date_depart'
);
$stmt->execute([$id]);
$transports = $stmt->fetchAll();

json_response([
    'destination'  => $destination,
    'hebergements' => $hebergements,
    'activites'    => $activites,
    'transports'   => $transports,
]);
