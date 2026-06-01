<?php
declare(strict_types=1);

// http://localhost/voyagevista/api/register.php  (POST)
require __DIR__ . '/_helpers.php';
require __DIR__ . '/_db.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    json_error('Méthode non autorisée.', 405);
}

$body   = read_json_body();
$email  = trim((string)($body['email']        ?? ''));
$mdp    = (string)      ($body['mot_de_passe'] ?? '');
$nom    = trim((string)($body['nom']          ?? ''));
$prenom = trim((string)($body['prenom']       ?? ''));
$role   = (string)      ($body['role']         ?? 'voyageur');

// --- Validation côté serveur (on ne fait jamais confiance au client) ---
if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    json_error('Email invalide.');
}
if (strlen($mdp) < 8) {
    json_error('Le mot de passe doit faire au moins 8 caractères.');
}
if ($nom === '' || $prenom === '') {
    json_error('Nom et prénom obligatoires.');
}
if (!in_array($role, ['voyageur', 'hote'], true)) {
    json_error('Rôle invalide.'); // pas de création d'admin via l'API publique
}

// Email déjà pris ?
$stmt = db()->prepare('SELECT 1 FROM utilisateur WHERE email = ?');
$stmt->execute([$email]);
if ($stmt->fetch()) {
    json_error('Cet email est déjà utilisé.', 409);
}

// Hash du mot de passe (bcrypt), jamais stocké en clair.
$hash = password_hash($mdp, PASSWORD_DEFAULT);

$stmt = db()->prepare(
    'INSERT INTO utilisateur (email, mot_de_passe, nom, prenom, role)
     VALUES (?, ?, ?, ?, ?)'
);
$stmt->execute([$email, $hash, $nom, $prenom, $role]);

json_response(['message' => 'Compte créé.'], 201);
