<?php

require_once __DIR__ . '/../config/db.php';
require_once __DIR__ . '/session.php';

Session::start();

header('Content-Type: application/json');

$method = $_SERVER['REQUEST_METHOD'];
$uri    = rtrim(parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH), '/');
$uri    = str_replace('/AetheriaPhp', '', $uri);

// POST /api/login
if ($uri === '/api/login' && $method === 'POST') {
    $data = json_decode(file_get_contents('php://input'), true);

    if (empty($data['email']) || empty($data['password'])) {
        http_response_code(400);
        echo json_encode(['message' => 'Email and password are required']);
        exit;
    }

    try {
        $db   = Database::connect();
        $stmt = $db->prepare("SELECT id, username, password, role FROM users WHERE email = :email");
        $stmt->execute([':email' => $data['email']]);
        $user = $stmt->fetch();

        if (!$user || !password_verify($data['password'], $user['password'])) {
            http_response_code(401);
            echo json_encode(['message' => 'Invalid credentials']);
            exit;
        }

        Session::set($user['id'], $user['username'], $user['role']);

        echo json_encode([
            'message' => 'Login successful',
            'user'    => [
                'id'       => $user['id'],
                'username' => $user['username'],
                'role'     => $user['role'],
            ]
        ]);

    } catch (PDOException $e) {
        http_response_code(500);
        echo json_encode(['message' => 'Server error']);
    }
    exit;
}

// POST /api/logout
if ($uri === '/api/logout' && $method === 'POST') {
    if (!Session::isLoggedIn()) {
        http_response_code(401);
        echo json_encode(['message' => 'Not logged in']);
        exit;
    }

    Session::destroy();
    echo json_encode(['message' => 'Logged out successfully']);
    exit;
}

http_response_code(405);
echo json_encode(['message' => 'Method not allowed']);
