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
    'SELECT st.id, st.id_sejour, st.id_transport, st.nb_passagers, st.prix
     FROM sejour_transport st
     JOIN sejour s ON s.id_sejour = st.id_sejour
     WHERE st.id = ? AND s.id_voyageur = ? AND s.statut = "brouillon"'
);
$stmt->execute([$id, $user['id']]);
$row = $stmt->fetch();
if (!$row) { $pdo->rollBack(); json_error('Élément introuvable ou séjour non modifiable.', 404); }

$pdo->prepare('UPDATE transport SET places_dispo = places_dispo + ? WHERE id_transport = ?')->execute([$row['nb_passagers'], $row['id_transport']]);
$pdo->prepare('DELETE FROM sejour_transport WHERE id = ?')->execute([$id]);
$pdo->prepare('UPDATE sejour SET prix_total = (SELECT COALESCE(SUM(sh.prix),0) FROM sejour_hebergement sh WHERE sh.id_sejour = ?) + (SELECT COALESCE(SUM(sa.prix),0) FROM sejour_activite sa WHERE sa.id_sejour = ?) + (SELECT COALESCE(SUM(st2.prix),0) FROM sejour_transport st2 WHERE st2.id_sejour = ?) WHERE id_sejour = ?')
    ->execute([$row['id_sejour'], $row['id_sejour'], $row['id_sejour'], $row['id_sejour']]);

$pdo->commit();
json_response(['message' => 'Transport retiré du séjour.']);
