<?php
require_once __DIR__ . '/../config/db.php';
require_once __DIR__ . '/../models/user.model.php';

class UserController {
    private UserModel $userModel;

    public function __construct() {
        $this->userModel = new UserModel();
    }

    public function read(): void {
        $users = $this->userModel->getAllUsers();
        header('Content-Type: application/json');
        echo json_encode($users);
    }

    public function readOne(int $id): void {
        $user = $this->userModel->getUserById($id);
        if ($user) {
            header('Content-Type: application/json');
            echo json_encode($user);
        } else {
            http_response_code(404);
            echo json_encode(['message' => 'User not found']);
        }
    }

    public function create(array $data): void {
        $user = new UserModel();
        $user->username = $data['username'];
        $user->email = $data['email'];
        $user->password = password_hash($data['password'], PASSWORD_BCRYPT);
        $user->description = $data['description'] ?? '';

        if ($user->create()) {
            http_response_code(201);
            echo json_encode(['message' => 'User created successfully']);
        } else {
            http_response_code(500);
            echo json_encode(['message' => 'Failed to create user']);
        }
    }

    public function update(int $id, array $data): void {
        $user = $this->userModel->getUserById($id);
        if (!$user) {
            http_response_code(404);
            echo json_encode(['message' => 'User not found']);
            return;
        }

        $user->username = $data['username'] ?? $user->username;
        $user->email = $data['email'] ?? $user->email;
        $user->description = $data['description'] ?? $user->description;
        $user->password = isset($data['password'])
            ? password_hash($data['password'], PASSWORD_BCRYPT)
            : $user->password;

        if ($user->updateUser($id)) {
            echo json_encode(['message' => 'User updated successfully']);
        } else {
            http_response_code(500);
            echo json_encode(['message' => 'Failed to update user']);
        }
    }

    public function delete(int $id): void {
        if ($this->userModel->deleteUser($id)) {
            echo json_encode(['message' => 'User deleted successfully']);
        } else {
            http_response_code(500);
            echo json_encode(['message' => 'Failed to delete user']);
        }
    }

    public function changeRole(int $id, string $role): void {
        if ($this->userModel->changeRole($id, $role)) {
            echo json_encode(['message' => 'User role updated successfully']);
        } else {
            http_response_code(500);
            echo json_encode(['message' => 'Failed to update user role']);
        }
    }
}
