<?php

require_once __DIR__ . '/config/db.php';
require_once __DIR__ . '/auth/session.php';

Session::start();

$uri = rtrim(parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH), '/');
$uri = str_replace('/AetheriaPhp', '', $uri);

if ($uri === '/api/login' || $uri === '/api/logout') {
    require __DIR__ . '/auth/login.php';
}
if ($uri === '/api/me') {
    header('Content-Type: application/json');
    if (Session::isLoggedIn()) {
        echo json_encode([
            'id'       => Session::get('user_id'),
            'username' => Session::get('username'),
            'role'     => Session::get('role'),
        ]);
    } else {
        http_response_code(401);
        echo json_encode(['message' => 'Not logged in']);
    }
    exit;
}
if (str_starts_with($uri, '/api/achievements')) {
    require __DIR__ . '/routes/achievement.route.php';
}
if (str_starts_with($uri, '/api/levels')) {
    require __DIR__ . '/routes/level.route.php';
}
if (str_starts_with($uri, '/api/games')) {
    require __DIR__ . '/routes/game.route.php';
}
if (str_starts_with($uri, '/api/users')) {
    require __DIR__ . '/routes/user.route.php';
}
