<?php

class LevelModel {
    private string $table = 'levels';

    public int $id;
    public int $game_id;
    public string $difficulty;
    public string $description;

    private PDO $db;

    public function __construct() {
        $this->db = Database::connect();
    }

    public function hydrate(array $row): self {
        $this->id          = (int)$row['id'];
        $this->game_id     = (int)$row['game_id'];
        $this->difficulty  = $row['difficulty'];
        $this->description = $row['description'] ?? '';
        return $this;
    }

    // Get all games linked to a difficulty
    public function getGamesByDifficulty(string $difficulty): array {
        $stmt = $this->db->prepare("
            SELECT g.* FROM games g
            INNER JOIN {$this->table} l ON g.id = l.game_id
            WHERE l.difficulty = :difficulty
        ");
        $stmt->execute([':difficulty' => $difficulty]);
        return $stmt->fetchAll();
    }

    // Add a game to a difficulty level
    public function addGameToLevel(): bool {
        
        $stmt = $this->db->prepare("INSERT INTO {$this->table} (game_id, difficulty, description) VALUES (:game_id, :difficulty, :description)");
        return $stmt->execute([
            ':game_id'     => $this->game_id,
            ':difficulty'  => $this->difficulty,
            ':description' => $this->description,
        ]);
    }
}
