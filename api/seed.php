<?php
declare(strict_types=1);

/**
 * Script de peuplement de la base avec des données de test (DEV uniquement).
 * À lancer une fois : http://localhost/voyagevista/api/seed.php?confirm=1
 * À SUPPRIMER avant le rendu final (ne doit pas finir en production).
 *
 * Il vide d'abord toutes les tables puis insère un jeu cohérent de données.
 */
require __DIR__ . '/_helpers.php';
require __DIR__ . '/_db.php';

// Garde-fou : on n'exécute pas par accident.
if (($_GET['confirm'] ?? '') !== '1') {
    json_error('Ajoutez ?confirm=1 à l\'URL pour exécuter le peuplement.', 400);
}

$pdo = db();

// 1) Réinitialisation (TRUNCATE valide une transaction implicitement -> hors transaction).
$pdo->exec('SET FOREIGN_KEY_CHECKS = 0');
foreach ([
    'paiement', 'sejour_transport', 'sejour_activite', 'sejour_hebergement',
    'notification', 'sejour', 'creneau', 'activite', 'disponibilite',
    'transport', 'hebergement', 'destination', 'utilisateur',
] as $table) {
    $pdo->exec("TRUNCATE TABLE `$table`");
}
$pdo->exec('SET FOREIGN_KEY_CHECKS = 1');

// 2) Insertions dans une transaction (tout ou rien).
$pdo->beginTransaction();

// --- Utilisateurs ---
$insUser = $pdo->prepare(
    'INSERT INTO utilisateur (email, mot_de_passe, nom, prenom, role)
     VALUES (?, ?, ?, ?, ?)'
);
$mdp = password_hash('motdepasse1', PASSWORD_DEFAULT); // même mdp de test pour tous
$insUser->execute(['admin@voyagevista.fr', $mdp, 'Martin', 'Alice', 'admin']);
$idAdmin = (int)$pdo->lastInsertId();
$insUser->execute(['hote1@voyagevista.fr', $mdp, 'Sousa', 'Tomas', 'hote']);
$idHote1 = (int)$pdo->lastInsertId();
$insUser->execute(['hote2@voyagevista.fr', $mdp, 'Tanaka', 'Yuki', 'hote']);
$idHote2 = (int)$pdo->lastInsertId();
$insUser->execute(['voyageur@voyagevista.fr', $mdp, 'Durand', 'Léa', 'voyageur']);

// --- Destinations ---
$insDest = $pdo->prepare(
    'INSERT INTO destination (id_admin, nom, pays, region, latitude, longitude, description)
     VALUES (?, ?, ?, ?, ?, ?, ?)'
);
$insDest->execute([$idAdmin, 'Lisbonne', 'Portugal', 'Estrémadure', 38.722252, -9.139337,
    'Capitale lumineuse, idéale pour un long séjour entre quartiers historiques et bord de mer.']);
$idLisbonne = (int)$pdo->lastInsertId();
$insDest->execute([$idAdmin, 'Kyoto', 'Japon', 'Kansaï', 35.011665, 135.768326,
    'Ancienne capitale impériale, temples et rythme lent propices à l\'immersion.']);
$idKyoto = (int)$pdo->lastInsertId();
$insDest->execute([$idAdmin, 'Oaxaca', 'Mexique', 'Oaxaca', 17.073185, -96.726593,
    'Ville coloniale, gastronomie et artisanat, parfaite pour s\'installer plusieurs semaines.']);
$idOaxaca = (int)$pdo->lastInsertId();
$insDest->execute([$idAdmin, 'Chiang Mai', 'Thaïlande', 'Nord', 18.787747, 98.993128,
    'Ville paisible entourée de montagnes, prisée des voyageurs au long cours.']);
$idChiangMai = (int)$pdo->lastInsertId();

// --- Hébergements (tarif HEBDOMADAIRE, durée min en nuits) ---
$insHeb = $pdo->prepare(
    'INSERT INTO hebergement (id_destination, id_hote, nom, type, description, capacite, prix_semaine, duree_min_nuits)
     VALUES (?, ?, ?, ?, ?, ?, ?, ?)'
);
$insHeb->execute([$idLisbonne, $idHote1, 'Appartement Alfama', 'appartement',
    'Au cœur du vieux quartier, lumineux, idéal pour un mois.', 2, 420.00, 7]);
$idHebLis = (int)$pdo->lastInsertId();
$insHeb->execute([$idKyoto, $idHote2, 'Machiya traditionnelle', 'maison',
    'Maison en bois typique, jardin intérieur, calme absolu.', 4, 690.00, 14]);
$idHebKyo = (int)$pdo->lastInsertId();
$insHeb->execute([$idOaxaca, $idHote1, 'Casa Colonial', 'maison',
    'Patio fleuri, cuisine équipée, proche du marché.', 3, 350.00, 7]);
$idHebOax = (int)$pdo->lastInsertId();
$insHeb->execute([$idChiangMai, $idHote2, 'Studio Nimman', 'appartement',
    'Quartier animé, café et coworking à proximité.', 2, 280.00, 7]);
$idHebCm = (int)$pdo->lastInsertId();

// --- Disponibilités (périodes ouvertes à la réservation) ---
$insDispo = $pdo->prepare(
    'INSERT INTO disponibilite (id_hebergement, date_debut, date_fin, statut)
     VALUES (?, ?, ?, "ouverte")'
);
$insDispo->execute([$idHebLis, '2026-06-01', '2026-06-30']);
$insDispo->execute([$idHebLis, '2026-07-01', '2026-07-31']);
$insDispo->execute([$idHebKyo, '2026-06-15', '2026-07-15']);
$insDispo->execute([$idHebOax, '2026-09-01', '2026-09-30']);
$insDispo->execute([$idHebCm,  '2026-06-01', '2026-08-31']);

// --- Activités ---
$insAct = $pdo->prepare(
    'INSERT INTO activite (id_destination, id_hote, nom, description, categorie, prix_personne, duree_h, niveau)
     VALUES (?, ?, ?, ?, ?, ?, ?, ?)'
);
$insAct->execute([$idLisbonne, $idHote1, 'Atelier de pastéis de nata', 'Cuisine locale avec un chef du quartier.',
    'gastronomie', 45.00, 3.0, 'facile']);
$idActLis = (int)$pdo->lastInsertId();
$insAct->execute([$idKyoto, $idHote2, 'Cérémonie du thé', 'Initiation à la cérémonie traditionnelle.',
    'culture', 60.00, 2.0, 'facile']);
$idActKyo = (int)$pdo->lastInsertId();
$insAct->execute([$idChiangMai, $idHote2, 'Randonnée au Doi Suthep', 'Marche guidée jusqu\'au temple en montagne.',
    'nature', 35.00, 5.0, 'moyen']);
$idActCm = (int)$pdo->lastInsertId();

// --- Créneaux d'activités ---
$insCren = $pdo->prepare(
    'INSERT INTO creneau (id_activite, date_heure, places_totales, places_restantes)
     VALUES (?, ?, ?, ?)'
);
$insCren->execute([$idActLis, '2026-06-10 10:00:00', 8, 8]);
$insCren->execute([$idActLis, '2026-06-17 10:00:00', 8, 6]);
$insCren->execute([$idActKyo, '2026-06-20 14:00:00', 6, 6]);
$insCren->execute([$idActCm,  '2026-06-05 08:00:00', 10, 10]);

// --- Transports (départ Paris vers les destinations) ---
$insTr = $pdo->prepare(
    'INSERT INTO transport (id_destination_arr, ville_depart, type, compagnie, date_depart, date_arrivee, prix, places_dispo)
     VALUES (?, ?, ?, ?, ?, ?, ?, ?)'
);
$insTr->execute([$idLisbonne, 'Paris', 'avion', 'TAP Air', '2026-06-01 08:30:00', '2026-06-01 11:00:00', 180.00, 40]);
$insTr->execute([$idKyoto, 'Paris', 'avion', 'ANA', '2026-06-15 13:00:00', '2026-06-16 09:00:00', 850.00, 25]);
$insTr->execute([$idChiangMai, 'Paris', 'avion', 'Thai Airways', '2026-06-01 12:00:00', '2026-06-02 10:00:00', 720.00, 30]);

$pdo->commit();

json_response(['message' => 'Base peuplée avec succès. Mot de passe de test : motdepasse1']);
