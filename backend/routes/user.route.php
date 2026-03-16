<?php

require_once __DIR__ . '/../controllers/user.controller.php';
require_once __DIR__ . '/../controllers/userGame.controller.php';

$controller     = new UserController();
$gameController = new UserGameController();

$method = $_SERVER['REQUEST_METHOD'];
$uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);

$uri = rtrim($uri, '/');
$uri = str_replace('/AetheriaPhp', '', $uri);

// GET /api/users/{id}/games
if (preg_match('/\/api\/users\/(\d+)\/games$/', $uri, $matches) && $method === 'GET') {
    $gameController->getGames((int)$matches[1]);
}
// POST /api/users/{id}/games
if (preg_match('/\/api\/users\/(\d+)\/games$/', $uri, $matches) && $method === 'POST') {
    $data = json_decode(file_get_contents('php://input'), true);
    if (!is_array($data)) {
        http_response_code(400);
        header('Content-Type: application/json');
        echo json_encode(['message' => 'Invalid JSON body']);
        exit;
    }
    $gameController->addGame((int)$matches[1], $data);
}
// DELETE /api/users/{id}/games/{game_id}
if (preg_match('/\/api\/users\/(\d+)\/games\/(\d+)$/', $uri, $matches) && $method === 'DELETE') {
    $gameController->removeGame((int)$matches[1], (int)$matches[2]);
}

// GET /api/users
if ($uri === '/api/users' && $method === 'GET') {
    $controller->read();
}
// GET /api/users/{id}
if (preg_match('/\/api\/users\/(\d+)$/', $uri, $matches) && $method === 'GET') {
    $controller->readOne((int)$matches[1]);
}
// POST /api/users
if ($uri === '/api/users' && $method === 'POST') {
    $data = json_decode(file_get_contents('php://input'), true);
    $controller->create($data);
}
// PUT /api/users/{id}
if (preg_match('/\/api\/users\/(\d+)$/', $uri, $matches) && $method === 'PUT') {
    $data = json_decode(file_get_contents('php://input'), true);
    $controller->update((int)$matches[1], $data);
}
// DELETE /api/users/{id}
if (preg_match('/\/api\/users\/(\d+)$/', $uri, $matches) && $method === 'DELETE') {
    $controller->delete((int)$matches[1]);
}
// PATCH /api/users/{id}/role
if (preg_match('/\/api\/users\/(\d+)\/role$/', $uri, $matches) && $method === 'PATCH') {
    $data = json_decode(file_get_contents('php://input'), true);
    $controller->changeRole((int)$matches[1], $data['role']);
}