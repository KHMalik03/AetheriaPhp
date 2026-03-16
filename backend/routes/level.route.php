<?php

require_once __DIR__ . '/../controllers/level.controller.php';

$controller = new LevelController();

$method = $_SERVER['REQUEST_METHOD'];
$uri    = rtrim(parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH), '/');
$uri    = str_replace('/AetheriaPhp', '', $uri);

// GET /api/levels/{difficulty}/games
if (preg_match('/\/api\/levels\/([^\/]+)\/games/', $uri, $matches) && $method === 'GET') {
    $controller->readGamesByDifficulty($matches[1]);
}
// POST /api/levels
if ($uri === '/api/levels' && $method === 'POST') {
    $data = json_decode(file_get_contents('php://input'), true);
    if (!is_array($data)) {
        http_response_code(400);
        header('Content-Type: application/json');
        echo json_encode(['message' => 'Invalid JSON body']);
        exit;
    }
    $controller->addGameToLevel($data);
}
