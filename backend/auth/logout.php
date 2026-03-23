<?php

require_once __DIR__ . '/session.php';

Session::start();

// Détruit la session
Session::destroy();

// Redirige vers la page d'accueil
header("Location: /AetheriaPhp/index.php");
exit;