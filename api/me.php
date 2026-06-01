<?php
declare(strict_types=1);

// http://localhost/voyagevista/api/me.php  (GET)
require __DIR__ . '/_helpers.php';

$user = require_auth(); // renvoie 401 si personne n'est connecté

json_response(['user' => $user]);
