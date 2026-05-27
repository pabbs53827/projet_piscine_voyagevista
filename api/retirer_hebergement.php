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
    'SELECT sh.id, sh.id_sejour, sh.id_dispo, sh.prix
     FROM sejour_hebergement sh
     JOIN sejour s ON s.id_sejour = sh.id_sejour
     WHERE sh.id = ? AND s.id_voyageur = ? AND s.statut = "brouillon"'
);
$stmt->execute([$id, $user['id']]);
$row = $stmt->fetch();
if (!$row) { $pdo->rollBack(); json_error('Élément introuvable ou séjour non modifiable.', 404); }

$pdo->prepare('UPDATE disponibilite SET statut = "ouverte" WHERE id_dispo = ?')->execute([$row['id_dispo']]);
$pdo->prepare('DELETE FROM sejour_hebergement WHERE id = ?')->execute([$id]);
$pdo->prepare('UPDATE sejour SET prix_total = (SELECT COALESCE(SUM(sh2.prix),0) FROM sejour_hebergement sh2 WHERE sh2.id_sejour = ?) + (SELECT COALESCE(SUM(sa.prix),0) FROM sejour_activite sa WHERE sa.id_sejour = ?) + (SELECT COALESCE(SUM(st.prix),0) FROM sejour_transport st WHERE st.id_sejour = ?) WHERE id_sejour = ?')
    ->execute([$row['id_sejour'], $row['id_sejour'], $row['id_sejour'], $row['id_sejour']]);

$pdo->commit();
json_response(['message' => 'Hébergement retiré du séjour.']);
