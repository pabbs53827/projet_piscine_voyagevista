<?php
declare(strict_types=1);
require __DIR__ . '/_helpers.php';
require __DIR__ . '/_db.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') json_error('Méthode non autorisée.', 405);
$user = require_auth();
$body = read_json_body();
$id   = (int)($body['id_sejour'] ?? 0);
if ($id <= 0) json_error('Identifiant manquant.');

$pdo = db();
$pdo->beginTransaction();

$stmt = $pdo->prepare('SELECT * FROM sejour WHERE id_sejour = ? AND id_voyageur = ? AND statut = "valide"');
$stmt->execute([$id, $user['id']]);
$sejour = $stmt->fetch();
if (!$sejour) { $pdo->rollBack(); json_error('Séjour introuvable ou non annulable.', 404); }

// Restaurer disponibilités hébergements
$pdo->prepare(
    'UPDATE disponibilite dp
     JOIN sejour_hebergement sh ON sh.id_dispo = dp.id_dispo
     SET dp.statut = "ouverte"
     WHERE sh.id_sejour = ?'
)->execute([$id]);

// Restaurer places créneaux activités
$pdo->prepare(
    'UPDATE creneau c
     JOIN sejour_activite sa ON sa.id_creneau = c.id_creneau
     SET c.places_restantes = c.places_restantes + sa.nb_participants
     WHERE sa.id_sejour = ?'
)->execute([$id]);

// Collecter les transports avant suppression des lignes de jonction
$stmtTr = $pdo->prepare(
    'SELECT id_transport, nb_passagers, sens FROM sejour_transport WHERE id_sejour = ?'
);
$stmtTr->execute([$id]);
$trRows = $stmtTr->fetchAll();

// Restaurer places pour les allers
foreach ($trRows as $row) {
    if ($row['sens'] === 'aller') {
        $pdo->prepare('UPDATE transport SET places_dispo = places_dispo + ? WHERE id_transport = ?')
            ->execute([$row['nb_passagers'], $row['id_transport']]);
    }
}

// Supprimer les lignes sejour_transport d'abord (lève la contrainte FK)
$pdo->prepare('DELETE FROM sejour_transport WHERE id_sejour = ?')->execute([$id]);

// Supprimer les enregistrements transport retour créés à la volée
foreach ($trRows as $row) {
    if ($row['sens'] === 'retour') {
        $pdo->prepare('DELETE FROM transport WHERE id_transport = ?')->execute([$row['id_transport']]);
    }
}

// Passer le séjour en annulé
$pdo->prepare('UPDATE sejour SET statut = "annule" WHERE id_sejour = ?')->execute([$id]);

$pdo->commit();
json_response(['message' => 'Séjour annulé avec succès.']);
