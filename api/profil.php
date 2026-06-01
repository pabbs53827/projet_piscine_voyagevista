<?php
declare(strict_types=1);
require __DIR__ . '/_helpers.php';
require __DIR__ . '/_db.php';

$user = require_auth();
$pdo  = db();

if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    $stmt = $pdo->prepare('SELECT id_utilisateur,email,nom,prenom,role,statut,date_inscription FROM utilisateur WHERE id_utilisateur = ?');
    $stmt->execute([$user['id']]);
    json_response(['user' => $stmt->fetch()]);
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $body   = read_json_body();
    $nom    = trim((string)($body['nom']    ?? ''));
    $prenom = trim((string)($body['prenom'] ?? ''));
    $mdp    = (string)($body['mot_de_passe'] ?? '');

    if ($nom === '' || $prenom === '') json_error('Nom et prénom obligatoires.');

    if ($mdp !== '') {
        if (strlen($mdp) < 8) json_error('Le mot de passe doit faire au moins 8 caractères.');
        $hash = password_hash($mdp, PASSWORD_DEFAULT);
        $pdo->prepare('UPDATE utilisateur SET nom=?, prenom=?, mot_de_passe=? WHERE id_utilisateur=?')
            ->execute([$nom, $prenom, $hash, $user['id']]);
    } else {
        $pdo->prepare('UPDATE utilisateur SET nom=?, prenom=? WHERE id_utilisateur=?')
            ->execute([$nom, $prenom, $user['id']]);
    }

    $_SESSION['user']['nom']    = $nom;
    $_SESSION['user']['prenom'] = $prenom;

    json_response(['message' => 'Profil mis à jour.']);
}

json_error('Méthode non autorisée.', 405);
