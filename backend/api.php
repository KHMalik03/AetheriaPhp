<?php
session_start();

require_once __DIR__ . '/config/db.php';

$uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);

if ($uri === '/AetheriaPhp/api/login') {
    require __DIR__ . '/auth/login.php';
}
if (str_starts_with($uri, '/AetheriaPhp/api/games')){
    require __DIR__ . '/routes/game.route.php';
}
if (str_starts_with($uri, '/AetheriaPhp/api/users')){
    require __DIR__ . '/routes/user.route.php';
}