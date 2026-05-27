<?php
declare(strict_types=1);
require __DIR__ . '/_helpers.php';
require __DIR__ . '/_db.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') json_error('Méthode non autorisée.', 405);
$user     = require_auth();
$body     = read_json_body();
$id       = (int)($body['id']          ?? 0); // id sejour_hebergement
$id_dispo_new = (int)($body['id_dispo'] ?? 0);
if ($id <= 0 || $id_dispo_new <= 0) json_error('Paramètres manquants.');

$pdo = db();
$pdo->beginTransaction();

// Vérifier que la ligne appartient au voyageur et que le séjour est brouillon
$stmt = $pdo->prepare(
    'SELECT sh.id, sh.id_sejour, sh.id_dispo, dp_old.id_hebergement
     FROM sejour_hebergement sh
     JOIN disponibilite dp_old ON dp_old.id_dispo = sh.id_dispo
     JOIN sejour s ON s.id_sejour = sh.id_sejour
     WHERE sh.id = ? AND s.id_voyageur = ? AND s.statut = "brouillon"'
);
$stmt->execute([$id, $user['id']]);
$row = $stmt->fetch();
if (!$row) { $pdo->rollBack(); json_error('Réservation introuvable ou non modifiable.', 404); }

// Vérifier que la nouvelle dispo appartient au même hébergement et est ouverte
$stmt2 = $pdo->prepare(
    'SELECT id_dispo, id_hebergement, date_debut, date_fin
     FROM disponibilite WHERE id_dispo = ? AND id_hebergement = ? AND statut = "ouverte"'
);
$stmt2->execute([$id_dispo_new, $row['id_hebergement']]);
$newDispo = $stmt2->fetch();
if (!$newDispo) { $pdo->rollBack(); json_error('Période indisponible.', 409); }

// Recalculer le prix selon le nombre de semaines
$stmt3 = $pdo->prepare('SELECT prix_semaine FROM hebergement WHERE id_hebergement = ?');
$stmt3->execute([$row['id_hebergement']]);
$heb = $stmt3->fetch();
$semaines = max(1, ceil((strtotime($newDispo['date_fin']) - strtotime($newDispo['date_debut'])) / (7 * 86400)));
$newPrix  = round($heb['prix_semaine'] * $semaines, 2);

// Libérer l'ancienne dispo, verrouiller la nouvelle
$pdo->prepare('UPDATE disponibilite SET statut = "ouverte"   WHERE id_dispo = ?')->execute([$row['id_dispo']]);
$pdo->prepare('UPDATE disponibilite SET statut = "reservee"  WHERE id_dispo = ?')->execute([$id_dispo_new]);
$pdo->prepare('UPDATE sejour_hebergement SET id_dispo = ?, prix = ? WHERE id = ?')->execute([$id_dispo_new, $newPrix, $id]);

// Recalcul prix_total
$pdo->prepare('UPDATE sejour SET prix_total = (SELECT COALESCE(SUM(sh2.prix),0) FROM sejour_hebergement sh2 WHERE sh2.id_sejour = ?) + (SELECT COALESCE(SUM(sa.prix),0) FROM sejour_activite sa WHERE sa.id_sejour = ?) + (SELECT COALESCE(SUM(st.prix),0) FROM sejour_transport st WHERE st.id_sejour = ?) WHERE id_sejour = ?')
    ->execute([$row['id_sejour'], $row['id_sejour'], $row['id_sejour'], $row['id_sejour']]);

$pdo->commit();
json_response(['message' => 'Réservation modifiée.', 'nouveau_prix' => $newPrix]);
