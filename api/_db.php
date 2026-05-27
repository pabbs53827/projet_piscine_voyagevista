<?php
declare(strict_types=1);

/**
 * Connexion à la base, créée une seule fois puis réutilisée.
 * On l'inclut dans chaque endpoint qui a besoin de la base.
 */
function db(): PDO
{
    static $pdo = null;

    if ($pdo === null) {
        $cfg = require __DIR__ . '/config.php';

        $dsn = "mysql:host={$cfg['host']};port={$cfg['port']};dbname={$cfg['dbname']};charset=utf8mb4";

        $pdo = new PDO($dsn, $cfg['user'], $cfg['password'], [
            PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION, // erreurs SQL -> exceptions
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,       // résultats en tableaux associatifs
            PDO::ATTR_EMULATE_PREPARES   => false,                 // vraies requêtes préparées (anti-injection)
        ]);
    }

    return $pdo;
}
