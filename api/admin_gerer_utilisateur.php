<?php
declare(strict_types=1);
require __DIR__ . '/_helpers.php';
require __DIR__ . '/_db.php';

$admin = require_role('admin');
$pdo   = db();

if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    $stmt = $pdo->query('SELECT id_utilisateur,email,nom,prenom,role,statut,date_inscription FROM utilisateur ORDER BY role,nom');
    json_response(['utilisateurs' => $stmt->fetchAll()]);
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $body   = read_json_body();
    $id     = (int)($body['id'] ?? 0);
    $action = (string)($body['action'] ?? '');
    if ($id <= 0) json_error('Identifiant manquant.');
    if ($id === $admin['id']) json_error('Vous ne pouvez pas modifier votre propre compte ici.');

    $stmt = $pdo->prepare('SELECT id_utilisateur,role FROM utilisateur WHERE id_utilisateur = ?');
    $stmt->execute([$id]);
    $target = $stmt->fetch();
    if (!$target) json_error('Utilisateur introuvable.', 404);

    match($action) {
        'promote_admin' => $pdo->prepare('UPDATE utilisateur SET role="admin" WHERE id_utilisateur=?')->execute([$id]),
        'promote_hote'  => $pdo->prepare('UPDATE utilisateur SET role="hote"  WHERE id_utilisateur=?')->execute([$id]),
        'demote'        => $pdo->prepare('UPDATE utilisateur SET role="voyageur" WHERE id_utilisateur=?')->execute([$id]),
        'suspend'       => $pdo->prepare('UPDATE utilisateur SET statut="suspendu" WHERE id_utilisateur=?')->execute([$id]),
        'activer'       => $pdo->prepare('UPDATE utilisateur SET statut="actif" WHERE id_utilisateur=?')->execute([$id]),
        default         => json_error('Action inconnue.')
    };

    json_response(['message' => 'Utilisateur mis à jour.']);
}

json_error('Méthode non autorisée.', 405);
