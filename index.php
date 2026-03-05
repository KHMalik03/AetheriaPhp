<?php
session_start();

require_once 'backend/config/db.php';

$uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);

if (str_starts_with($uri, '/AetheriaPhp/api/games')){
    require 'backend/routes/game.route.php';
}
if (str_starts_with($uri, '/AetheriaPhp/api/users')){
    require 'backend/routes/user.route.php';
}