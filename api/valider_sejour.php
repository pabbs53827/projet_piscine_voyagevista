<?php
declare(strict_types=1);

// POST — valide le séjour brouillon et crée un paiement simulé
require __DIR__ . '/_helpers.php';
require __DIR__ . '/_db.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') json_error('Méthode non autorisée.', 405);

$user = require_auth();
$pdo  = db();

$stmt = $pdo->prepare('SELECT * FROM sejour WHERE id_voyageur = ? AND statut = "brouillon" ORDER BY date_creation DESC LIMIT 1');
$stmt->execute([$user['id']]);
$sejour = $stmt->fetch();
if (!$sejour) json_error('Aucun séjour en cours à valider.');

// Il faut au moins un hébergement pour valider.
$stmt = $pdo->prepare('SELECT COUNT(*) FROM sejour_hebergement WHERE id_sejour = ?');
$stmt->execute([$sejour['id_sejour']]);
if ((int)$stmt->fetchColumn() === 0) {
    json_error('Ajoutez au moins un hébergement avant de valider votre séjour.');
}

// Référence lisible type VV-2026-00001
$ref = 'VV-' . date('Y') . '-' . str_pad((string)$sejour['id_sejour'], 5, '0', STR_PAD_LEFT);

$pdo->beginTransaction();

$pdo->prepare('UPDATE sejour SET statut = "valide" WHERE id_sejour = ?')->execute([$sejour['id_sejour']]);

$pdo->prepare(
    'INSERT INTO paiement (id_sejour, montant, statut, reference) VALUES (?, ?, "simule_ok", ?)'
)->execute([$sejour['id_sejour'], $sejour['prix_total'], $ref]);

// Notification de confirmation
$pdo->prepare(
    'INSERT INTO notification (id_utilisateur, id_sejour, type, titre, message)
     VALUES (?, ?, "reservation_confirmee", "Réservation confirmée !", ?)'
)->execute([
    $user['id'],
    $sejour['id_sejour'],
    "Votre séjour \"{$sejour['titre']}\" a été confirmé. Référence : $ref",
]);

$pdo->commit();

json_response([
    'message'   => 'Séjour validé avec succès.',
    'reference' => $ref,
    'montant'   => $sejour['prix_total'],
    'titre'     => $sejour['titre'],
]);
