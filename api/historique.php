<?php
declare(strict_types=1);

// GET — séjours validés de l'utilisateur connecté avec leurs éléments
require __DIR__ . '/_helpers.php';
require __DIR__ . '/_db.php';

if ($_SERVER['REQUEST_METHOD'] !== 'GET') json_error('Méthode non autorisée.', 405);

$user = require_auth();
$pdo  = db();

$stmt = $pdo->prepare(
    'SELECT * FROM sejour WHERE id_voyageur = ? AND statut = "valide" ORDER BY date_creation DESC'
);
$stmt->execute([$user['id']]);
$sejours = $stmt->fetchAll();

$result = [];

foreach ($sejours as $sejour) {
    $id = (int)$sejour['id_sejour'];

    $stmtH = $pdo->prepare(
        'SELECT sh.prix, dp.date_debut, dp.date_fin,
                h.id_hebergement, h.nom AS nom_hebergement, h.type, h.image_url,
                d.nom AS nom_destination, d.pays
         FROM sejour_hebergement sh
         JOIN disponibilite dp ON dp.id_dispo      = sh.id_dispo
         JOIN hebergement   h  ON h.id_hebergement = dp.id_hebergement
         JOIN destination   d  ON d.id_destination = h.id_destination
         WHERE sh.id_sejour = ?'
    );
    $stmtH->execute([$id]);
    $hebergements = $stmtH->fetchAll();

    $stmtA = $pdo->prepare(
        'SELECT sa.prix, sa.nb_participants, cr.date_heure,
                a.id_activite, a.nom AS nom_activite, a.categorie, a.image_url,
                d.nom AS nom_destination
         FROM sejour_activite sa
         JOIN creneau   cr ON cr.id_creneau = sa.id_creneau
         JOIN activite  a  ON a.id_activite  = cr.id_activite
         JOIN destination d ON d.id_destination = a.id_destination
         WHERE sa.id_sejour = ?'
    );
    $stmtA->execute([$id]);
    $activites = $stmtA->fetchAll();

    $stmtT = $pdo->prepare(
        'SELECT st.prix, st.nb_passagers,
                t.ville_depart, t.type, t.compagnie, t.date_depart, t.date_arrivee,
                d.nom AS destination
         FROM sejour_transport st
         JOIN transport   t ON t.id_transport   = st.id_transport
         JOIN destination d ON d.id_destination = t.id_destination_arr
         WHERE st.id_sejour = ?'
    );
    $stmtT->execute([$id]);
    $transports = $stmtT->fetchAll();

    $result[] = [
        'sejour'       => $sejour,
        'hebergements' => $hebergements,
        'activites'    => $activites,
        'transports'   => $transports,
    ];
}

json_response(['historique' => $result]);
