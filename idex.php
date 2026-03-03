<?php
session_start();

require_once '../config/db.php';

$uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);

if (str_starts_with($uri, '/game')){
    require '../routes/game.route.php';
}