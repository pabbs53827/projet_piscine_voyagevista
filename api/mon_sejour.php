<?php
declare(strict_types=1);

// GET — séjour brouillon avec tous ses éléments (hébergements, activités, transports)
require __DIR__ . '/_helpers.php';
require __DIR__ . '/_db.php';

if ($_SERVER['REQUEST_METHOD'] !== 'GET') json_error('Méthode non autorisée.', 405);

$user = require_auth();
$pdo  = db();

$stmt = $pdo->prepare('SELECT * FROM sejour WHERE id_voyageur = ? AND statut = "brouillon" ORDER BY date_creation DESC LIMIT 1');
$stmt->execute([$user['id']]);
$sejour = $stmt->fetch();

if (!$sejour) {
    json_response(['sejour' => null, 'hebergements' => [], 'activites' => [], 'transports' => [], 'total' => 0]);
}

$id = (int)$sejour['id_sejour'];

// Hébergements
$stmt = $pdo->prepare(
    'SELECT sh.id, sh.prix, dp.date_debut, dp.date_fin,
            h.id_hebergement, h.nom AS nom_hebergement, h.type
     FROM sejour_hebergement sh
     JOIN disponibilite dp ON dp.id_dispo      = sh.id_dispo
     JOIN hebergement   h  ON h.id_hebergement = dp.id_hebergement
     WHERE sh.id_sejour = ?'
);
$stmt->execute([$id]);
$hebergements = $stmt->fetchAll();

// Activités
$stmt = $pdo->prepare(
    'SELECT sa.id, sa.prix, sa.nb_participants, cr.date_heure,
            a.nom AS nom_activite, a.categorie
     FROM sejour_activite sa
     JOIN creneau  cr ON cr.id_creneau = sa.id_creneau
     JOIN activite a  ON a.id_activite = cr.id_activite
     WHERE sa.id_sejour = ?'
);
$stmt->execute([$id]);
$activites = $stmt->fetchAll();

// Transports
$stmt = $pdo->prepare(
    'SELECT st.id, st.prix, st.nb_passagers,
            t.ville_depart, t.type, t.compagnie, t.date_depart, t.date_arrivee,
            d.nom AS destination
     FROM sejour_transport st
     JOIN transport    t ON t.id_transport     = st.id_transport
     JOIN destination  d ON d.id_destination   = t.id_destination_arr
     WHERE st.id_sejour = ?'
);
$stmt->execute([$id]);
$transports = $stmt->fetchAll();

// Total recalculé depuis les lignes
$total = 0;
foreach ($hebergements as $h) $total += (float)$h['prix'];
foreach ($activites   as $a) $total += (float)$a['prix'];
foreach ($transports  as $t) $total += (float)$t['prix'];

json_response([
    'sejour'       => $sejour,
    'hebergements' => $hebergements,
    'activites'    => $activites,
    'transports'   => $transports,
    'total'        => round($total, 2),
]);
