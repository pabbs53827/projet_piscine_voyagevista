<?php
declare(strict_types=1);

/**
 * Fonctions partagées par tous les endpoints, et démarrage de la session.
 * À inclure en premier dans chaque fichier d'API.
 */

// Cookie de session sécurisé (à régler AVANT de démarrer la session).
session_set_cookie_params([
    'httponly' => true,   // cookie inaccessible au JavaScript
    'samesite' => 'Lax',  // protection de base contre le CSRF
]);
session_start();

// Toute exception non rattrapée -> 500 propre, sans fuite du détail SQL.
set_exception_handler(function (Throwable $e): void {
    error_log($e->getMessage());
    http_response_code(500);
    header('Content-Type: application/json; charset=utf-8');
    echo json_encode(['error' => 'Erreur serveur.'], JSON_UNESCAPED_UNICODE);
    exit;
});

/** Envoie une réponse JSON puis stoppe l'exécution. */
function json_response(array $data, int $status = 200): void
{
    http_response_code($status);
    header('Content-Type: application/json; charset=utf-8');
    echo json_encode($data, JSON_UNESCAPED_UNICODE);
    exit;
}

/** Raccourci pour une erreur JSON. */
function json_error(string $message, int $status = 400): void
{
    json_response(['error' => $message], $status);
}

/** Décode le corps JSON envoyé par React (fetch). */
function read_json_body(): array
{
    $raw  = file_get_contents('php://input');
    $data = json_decode($raw ?: '', true);
    return is_array($data) ? $data : [];
}

/** Utilisateur connecté (depuis la session) ou null. */
function current_user(): ?array
{
    return $_SESSION['user'] ?? null;
}

/** Exige une session valide, sinon 401. */
function require_auth(): array
{
    $user = current_user();
    if ($user === null) {
        json_error('Authentification requise.', 401);
    }
    return $user;
}

/** Exige un des rôles indiqués, sinon 403. */
function require_role(string ...$roles): array
{
    $user = require_auth();
    if (!in_array($user['role'], $roles, true)) {
        json_error('Accès refusé.', 403);
    }
    return $user;
}
