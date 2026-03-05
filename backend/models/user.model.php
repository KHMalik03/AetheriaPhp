<?php

class UserModel {
    //Table name
    private string $table = "users";

    //Attributes
    public int $id;
    public string $username;
    public string $email;
    public string $password;
    public string $role;
    public string $description;
    public string $created_at;

    //Connect to db
    private PDO $db;

    public function __construct() {
        $this->db = Database::connect();
    }

    //Hydrate method to populate the model from a database row
    public function hydrate(array $row): self {
        $this->id = (int)$row['id'];
        $this->username = $row['username'];
        $this->email = $row['email'];
        $this->password = $row['password'] ?? '';
        $this->role = $row['role'] ?? 'user';
        $this->description = $row['description'] ?? '';
        $this->created_at = $row['created_at'];
        return $this;
    }

    //CRUD operations
    public function create(): bool {
        $stmt = $this->db->prepare("INSERT INTO {$this->table} (username, email, password, description) VALUES (:username, :email, :password, :description)");
        return $stmt->execute([
            ':username'    => $this->username,
            ':email'       => $this->email,
            ':password'    => $this->password,
            ':description' => $this->description ?? '',
        ]);
    }

    public function getUserById(int $id): ?self {
        $stmt = $this->db->prepare("SELECT id, username, email, password, role, description, created_at FROM {$this->table} WHERE id = :id");
        $stmt->execute([':id' => $id]);
        $row = $stmt->fetch();
        return $row ? $this->hydrate($row) : null;
    }

    public function getAllUsers(): array {
        $stmt = $this->db->query("SELECT id, username, email, role, description, created_at FROM {$this->table}");
        $users = [];
        while ($row = $stmt->fetch()) {
            $users[] = (new self())->hydrate($row);
        }
        return $users;
    }

    public function updateUser(int $id): bool {
        $stmt = $this->db->prepare("UPDATE {$this->table} SET username = :username, email = :email, password = :password, description = :description WHERE id = :id");
        return $stmt->execute([
            ':username'    => $this->username,
            ':email'       => $this->email,
            ':password'    => $this->password,
            ':description' => $this->description ?? '',
            ':id'          => $id
        ]);
    }

    public function deleteUser(int $id): bool {
        $stmt = $this->db->prepare("DELETE FROM {$this->table} WHERE id = :id");
        return $stmt->execute([':id' => $id]);
    }

    public function changeRole(int $id, string $role): bool {
        $stmt = $this->db->prepare("UPDATE {$this->table} SET role = :role WHERE id = :id");
        return $stmt->execute([':id' => $id, ':role' => $role]);
    }
}
