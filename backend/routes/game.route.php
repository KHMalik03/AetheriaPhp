<?php

require_once __DIR__ . '/../controllers/game.controller.php';

$controller = new GameController();

$method = $_SERVER['REQUEST_METHOD'];
$uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);

$uri = rtrim($uri, '/');
$uri = str_replace('/AetheriaPhp', '', $uri);

if ($uri === '/api/games' && $method === 'GET') {
    $controller->read();
}
if (preg_match('/\/api\/games\/(\d+)/', $uri, $matches) && $method === 'GET') {
    $controller->readOne((int)$matches[1]);
}
if ($uri === '/api/games' && $method === 'POST') {
    $data = json_decode(file_get_contents('php://input'), true);
    $controller->create($data);
}
if (preg_match('/\/api\/games\/(\d+)/', $uri, $matches) && $method === 'PUT') {
    $data = json_decode(file_get_contents('php://input'), true);
    $controller->update((int)$matches[1], $data);
}
if (preg_match('/\/api\/games\/(\d+)/', $uri, $matches) && $method === 'DELETE') {
    $controller->delete((int)$matches[1]);
}