<?php
declare(strict_types=1);

// http://localhost/voyagevista/api/login.php  (POST)
require __DIR__ . '/_helpers.php';
require __DIR__ . '/_db.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    json_error('Méthode non autorisée.', 405);
}

$body  = read_json_body();
$email = trim((string)($body['email'] ?? ''));
$mdp   = (string)($body['mot_de_passe'] ?? '');

$stmt = db()->prepare(
    'SELECT id_utilisateur, mot_de_passe, nom, prenom, role, statut
     FROM utilisateur WHERE email = ?'
);
$stmt->execute([$email]);
$user = $stmt->fetch();

// Message générique volontaire : on ne révèle pas si l'email existe.
if (!$user || !password_verify($mdp, $user['mot_de_passe'])) {
    json_error('Identifiants incorrects.', 401);
}
if ($user['statut'] === 'suspendu') {
    json_error('Compte suspendu.', 403);
}

// Anti session-fixation : nouvel identifiant de session à la connexion.
session_regenerate_id(true);

// On ne garde en session que le nécessaire (jamais le hash du mot de passe).
$_SESSION['user'] = [
    'id'     => (int)$user['id_utilisateur'],
    'nom'    => $user['nom'],
    'prenom' => $user['prenom'],
    'role'   => $user['role'],
];

json_response(['user' => $_SESSION['user']]);
