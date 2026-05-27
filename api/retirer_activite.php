<?php
declare(strict_types=1);
require __DIR__ . '/_helpers.php';
require __DIR__ . '/_db.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') json_error('Méthode non autorisée.', 405);
$user = require_auth();
$body = read_json_body();
$id   = (int)($body['id'] ?? 0);
if ($id <= 0) json_error('Identifiant manquant.');

$pdo = db();
$pdo->beginTransaction();

$stmt = $pdo->prepare(
    'SELECT sa.id, sa.id_sejour, sa.id_creneau, sa.nb_participants, sa.prix
     FROM sejour_activite sa
     JOIN sejour s ON s.id_sejour = sa.id_sejour
     WHERE sa.id = ? AND s.id_voyageur = ? AND s.statut = "brouillon"'
);
$stmt->execute([$id, $user['id']]);
$row = $stmt->fetch();
if (!$row) { $pdo->rollBack(); json_error('Élément introuvable ou séjour non modifiable.', 404); }

$pdo->prepare('UPDATE creneau SET places_restantes = places_restantes + ? WHERE id_creneau = ?')->execute([$row['nb_participants'], $row['id_creneau']]);
$pdo->prepare('DELETE FROM sejour_activite WHERE id = ?')->execute([$id]);
$pdo->prepare('UPDATE sejour SET prix_total = (SELECT COALESCE(SUM(sh.prix),0) FROM sejour_hebergement sh WHERE sh.id_sejour = ?) + (SELECT COALESCE(SUM(sa2.prix),0) FROM sejour_activite sa2 WHERE sa2.id_sejour = ?) + (SELECT COALESCE(SUM(st.prix),0) FROM sejour_transport st WHERE st.id_sejour = ?) WHERE id_sejour = ?')
    ->execute([$row['id_sejour'], $row['id_sejour'], $row['id_sejour'], $row['id_sejour']]);

$pdo->commit();
json_response(['message' => 'Activité retirée du séjour.']);
