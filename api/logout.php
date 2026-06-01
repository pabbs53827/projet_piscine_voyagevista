<?php
declare(strict_types=1);

// http://localhost/voyagevista/api/logout.php  (POST)
require __DIR__ . '/_helpers.php';

$_SESSION = [];
session_destroy();

json_response(['message' => 'Déconnecté.']);
