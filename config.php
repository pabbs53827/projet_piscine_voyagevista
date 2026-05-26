<?php

define('APP_NAME', 'VoyageVista');
define('APP_VERSION', '1.0');
define('APP_URL', (isset($_SERVER['HTTPS']) ? 'https' : 'http') . '://' . $_SERVER['HTTP_HOST']);
define('SITE_NAME', 'VoyageVista - Planifiez Vos Voyages de Rêve');

// Décommentez et configurez pour la DB
/*
define('DB_HOST', 'localhost');
define('DB_USER', 'root');
define('DB_PASS', '');
define('DB_NAME', 'voyagevista_db');
define('DB_PORT', 3306);

// Try to connect (future)
try {
  $pdo = new PDO(
    "mysql:host=" . DB_HOST . ";dbname=" . DB_NAME . ";charset=utf8",
    DB_USER,
    DB_PASS,
    [
      PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
      PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    ]
  );
} catch (PDOException $e) {
  die("Erreur de connexion base de données: " . $e->getMessage());
}
*/

session_start();
ini_set('session.cookie_secure', isset($_SERVER['HTTPS']));
ini_set('session.cookie_httponly', true);
ini_set('session.cookie_samesite', 'Strict');

date_default_timezone_set('Europe/Paris');
setlocale(LC_TIME, 'fr_FR.UTF-8');

ini_set('display_errors', 1);
error_reporting(E_ALL);

function sanitize($input) {
  return htmlspecialchars(trim($input), ENT_QUOTES, 'UTF-8');
}

function getPageTitle($current_page = '') {
  $titles = [
    'index' => 'Accueil',
    'destinations' => 'Destinations',
    'experiences' => 'Expériences',
    'reservations' => 'Mes Réservations',
    'contact' => 'Nous Contacter',
  ];
  
  return $titles[$current_page] ?? 'VoyageVista';
}

function isLoggedIn() {
  return isset($_SESSION['user_id']);
}

function redirect($url) {
  header("Location: " . $url);
  exit();
}

function formatPrice($price) {
  return number_format($price, 2, ',', ' ') . ' €';
}

function getCurrentPage() {
  return basename($_SERVER['PHP_SELF'], '.php');
}

$api_endpoints = [
  'search_destinations' => 'api/search-destinations.php',
  'get_destination' => 'api/get-destination.php',
  'create_reservation' => 'api/create-reservation.php',
  'subscribe_newsletter' => 'api/subscribe-newsletter.php',
];

$destinations = [
  [
    'id' => 1,
    'name' => 'Maldives',
    'country' => 'Océan Indien',
    'price' => 1299,
    'rating' => 4.8,
    'reviews' => 2145,
    'description' => 'Séjours exotiques avec bungalows sur l\'eau, plages de sable blanc et récifs coralliens spectaculaires.',
    'features' => ['Plage Privée', 'Snorkeling', 'Spa Luxe'],
    'image' => 'assets/images/maldives.jpg'
  ],
  [
    'id' => 2,
    'name' => 'Bali',
    'country' => 'Indonésie',
    'price' => 799,
    'rating' => 4.7,
    'reviews' => 3421,
    'description' => 'Découvrez les temples anciens, les rizières verdoyantes et les plages de sable noir de Bali.',
    'features' => ['Temples', 'Yoga', 'Nature'],
    'image' => 'assets/images/bali.jpg'
  ],
];

define('ADMIN_EMAIL', 'admin@voyagevista.com');
define('SUPPORT_EMAIL', 'support@voyagevista.com');
define('NOREPLY_EMAIL', 'noreply@voyagevista.com');

?>
