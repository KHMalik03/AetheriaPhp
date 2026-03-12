<?php

class AchievementModel {
    private string $table = 'achievements';

    public int $id;
    public int $game_id;
    public string $title;
    public string $description;

    private PDO $db;

    public function __construct() {
        $this->db = Database::connect();
    }

    public function hydrate(array $row): self {
        $this->id          = (int)$row['id'];
        $this->game_id     = (int)$row['game_id'];
        $this->title       = $row['title'];
        $this->description = $row['description'];
        return $this;
    }

    public function getByGameId(int $game_id): array {
        $stmt = $this->db->prepare("SELECT * FROM {$this->table} WHERE game_id = :game_id");
        $stmt->execute([':game_id' => $game_id]);
        $achievements = [];
        while ($row = $stmt->fetch()) {
            $achievements[] = (new self())->hydrate($row);
        }
        return $achievements;
    }

    public function getById(int $id): ?self {
        $stmt = $this->db->prepare("SELECT * FROM {$this->table} WHERE id = :id");
        $stmt->execute([':id' => $id]);
        $row = $stmt->fetch();
        return $row ? $this->hydrate($row) : null;
    }

    public function create(): bool {
        $stmt = $this->db->prepare("INSERT INTO {$this->table} (game_id, title, description) VALUES (:game_id, :title, :description)");
        return $stmt->execute([
            ':game_id'     => $this->game_id,
            ':title'       => $this->title,
            ':description' => $this->description,
        ]);
    }

    public function update(): bool {
        $stmt = $this->db->prepare("UPDATE {$this->table} SET title = :title, description = :description WHERE id = :id");
        return $stmt->execute([
            ':title'       => $this->title,
            ':description' => $this->description,
            ':id'          => $this->id,
        ]);
    }

    public function delete(int $id): bool {
        $stmt = $this->db->prepare("DELETE FROM {$this->table} WHERE id = :id");
        return $stmt->execute([':id' => $id]);
    }
}
