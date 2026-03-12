<?php

require_once __DIR__ . '/../controllers/achievement.controller.php';

$controller = new AchievementController();

$method = $_SERVER['REQUEST_METHOD'];
$uri    = rtrim(parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH), '/');
$uri    = str_replace('/AetheriaPhp', '', $uri);

// GET /api/achievements/game/{game_id}
if (preg_match('/\/api\/achievements\/game\/(\d+)/', $uri, $matches) && $method === 'GET') {
    $controller->readByGame((int)$matches[1]);
}
// POST /api/achievements
if ($uri === '/api/achievements' && $method === 'POST') {
    $data = json_decode(file_get_contents('php://input'), true);
    $controller->create($data);
}
// PUT /api/achievements/{id}
if (preg_match('/\/api\/achievements\/(\d+)$/', $uri, $matches) && $method === 'PUT') {
    $data = json_decode(file_get_contents('php://input'), true);
    $controller->update((int)$matches[1], $data);
}
// DELETE /api/achievements/{id}
if (preg_match('/\/api\/achievements\/(\d+)$/', $uri, $matches) && $method === 'DELETE') {
    $controller->delete((int)$matches[1]);
}
