<?php
declare(strict_types=1);
require __DIR__ . '/_helpers.php';
require __DIR__ . '/_db.php';

if ($_SERVER['REQUEST_METHOD'] !== 'GET') json_error('Méthode non autorisée.', 405);
require_role('admin');
$pdo = db();

// Statistiques globales
$stats = [];
foreach ([
    'nb_utilisateurs'  => 'SELECT COUNT(*) FROM utilisateur',
    'nb_voyageurs'     => 'SELECT COUNT(*) FROM utilisateur WHERE role = "voyageur"',
    'nb_hotes'         => 'SELECT COUNT(*) FROM utilisateur WHERE role = "hote"',
    'nb_destinations'  => 'SELECT COUNT(*) FROM destination',
    'nb_hebergements'  => 'SELECT COUNT(*) FROM hebergement',
    'nb_sejours_valides' => 'SELECT COUNT(*) FROM sejour WHERE statut = "valide"',
] as $key => $sql) {
    $stats[$key] = (int)$pdo->query($sql)->fetchColumn();
}

// Destinations avec nb hébergements
$stmt = $pdo->query(
    'SELECT d.*,
       (SELECT COUNT(*) FROM hebergement h WHERE h.id_destination = d.id_destination) AS nb_hebergements
     FROM destination d ORDER BY d.nom'
);
$destinations = $stmt->fetchAll();

$utilisateurs = $pdo->query('SELECT id_utilisateur,email,nom,prenom,role,statut FROM utilisateur ORDER BY role,nom')->fetchAll();

// Séjours avec voyageur associé
$sejours = $pdo->query(
    'SELECT s.id_sejour, s.titre, s.statut, s.prix_total, s.date_debut, s.date_fin, s.date_creation,
            u.id_utilisateur, u.nom AS voyageur_nom, u.prenom AS voyageur_prenom, u.email AS voyageur_email
     FROM sejour s
     JOIN utilisateur u ON u.id_utilisateur = s.id_voyageur
     ORDER BY s.date_creation DESC'
)->fetchAll();

json_response(['stats' => $stats, 'destinations' => $destinations, 'utilisateurs' => $utilisateurs, 'sejours' => $sejours]);
