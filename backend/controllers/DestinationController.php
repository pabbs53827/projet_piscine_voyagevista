<?php
require_once __DIR__ . '/../models/DestinationModel.php';

header('Content-Type: application/json; charset=utf-8');
header('Access-Control-Allow-Origin: http://localhost:5173');
header('Access-Control-Allow-Methods: GET, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type');

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(204);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
    http_response_code(405);
    echo json_encode(['success' => false, 'message' => 'Méthode non autorisée']);
    exit;
}

$model = new DestinationModel();

if (isset($_GET['id'])) {
    $id = filter_var($_GET['id'], FILTER_VALIDATE_INT);
    if ($id === false || $id < 1) {
        http_response_code(400);
        echo json_encode(['success' => false, 'message' => 'Identifiant invalide']);
        exit;
    }
    $destination = $model->getById($id);
    if (!$destination) {
        http_response_code(404);
        echo json_encode(['success' => false, 'message' => 'Destination introuvable']);
        exit;
    }
    echo json_encode(['success' => true, 'data' => $destination]);
} else {
    $destinations = $model->getAll();
    echo json_encode([
        'success' => true,
        'count'   => count($destinations),
        'data'    => $destinations,
    ]);
}
