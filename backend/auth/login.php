<?php

require_once __DIR__ . '/../config/db.php';
require_once __DIR__ . '/session.php';

Session::start();

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['message' => 'Method not allowed']);
    exit;
}

$data = json_decode(file_get_contents('php://input'), true);

if (empty($data['email']) || empty($data['password'])) {
    http_response_code(400);
    echo json_encode(['message' => 'Email and password are required']);
    exit;
}

try {
    $db = Database::connect();
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
        'message'  => 'Login successful',
        'user'     => [
            'id'       => $user['id'],
            'username' => $user['username'],
            'role'     => $user['role'],
        ]
    ]);

} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode(['message' => 'Server error']);
}
