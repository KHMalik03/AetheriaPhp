<?php

class UserGameModel {
    private string $table = 'user_games';

    public int $user_id;
    public int $game_id;

    private PDO $db;

    public function __construct() {
        $this->db = Database::connect();
    }

    public function getByUserId(int $user_id): array {
        $stmt = $this->db->prepare("
            SELECT g.*, ug.date_added, ug.play_time
            FROM games g
            INNER JOIN {$this->table} ug ON g.id = ug.game_id
            WHERE ug.user_id = :user_id
        ");
        $stmt->execute([':user_id' => $user_id]);
        return $stmt->fetchAll();
    }

    public function add(): bool {
        $stmt = $this->db->prepare("
            INSERT INTO {$this->table} (user_id, game_id)
            VALUES (:user_id, :game_id)
        ");
        return $stmt->execute([
            ':user_id' => $this->user_id,
            ':game_id' => $this->game_id,
        ]);
    }

    public function remove(int $user_id, int $game_id): bool {
        $stmt = $this->db->prepare("
            DELETE FROM {$this->table}
            WHERE user_id = :user_id AND game_id = :game_id
        ");
        return $stmt->execute([
            ':user_id' => $user_id,
            ':game_id' => $game_id,
        ]);
    }
}
