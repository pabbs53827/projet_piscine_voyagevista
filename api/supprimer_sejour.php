<?php
declare(strict_types=1);
require __DIR__ . '/_helpers.php';
require __DIR__ . '/_db.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') json_error('Méthode non autorisée.', 405);
$user = require_auth();
$pdo  = db();

$stmt = $pdo->prepare('SELECT * FROM sejour WHERE id_voyageur = ? AND statut = "brouillon" ORDER BY date_creation DESC LIMIT 1');
$stmt->execute([$user['id']]);
$sejour = $stmt->fetch();
if (!$sejour) json_error('Aucun séjour en cours à supprimer.', 404);

$id = (int)$sejour['id_sejour'];
$pdo->beginTransaction();

// Restaurer les disponibilités
$pdo->prepare('UPDATE disponibilite dp JOIN sejour_hebergement sh ON sh.id_dispo = dp.id_dispo SET dp.statut = "ouverte" WHERE sh.id_sejour = ?')->execute([$id]);

// Restaurer les places créneaux
$pdo->prepare('UPDATE creneau c JOIN sejour_activite sa ON sa.id_creneau = c.id_creneau SET c.places_restantes = c.places_restantes + sa.nb_participants WHERE sa.id_sejour = ?')->execute([$id]);

// Restaurer les places transport
$pdo->prepare('UPDATE transport t JOIN sejour_transport st ON st.id_transport = t.id_transport SET t.places_dispo = t.places_dispo + st.nb_passagers WHERE st.id_sejour = ?')->execute([$id]);

// Supprimer le séjour (CASCADE supprime les lignes liées)
$pdo->prepare('DELETE FROM sejour WHERE id_sejour = ?')->execute([$id]);

$pdo->commit();
json_response(['message' => 'Séjour supprimé.']);
