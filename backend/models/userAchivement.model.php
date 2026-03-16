<?php

class UserAchievementModel {
    private string $table = 'user_achievements';

    public int $user_id;
    public int $achievement_id;

    private PDO $db;

    public function __construct() {
        $this->db = Database::connect();
    }

    public function getByUserId(int $user_id): array {
        $stmt = $this->db->prepare("
            SELECT a.*, ua.unlocked_at
            FROM achievements a
            INNER JOIN {$this->table} ua ON a.id = ua.achievement_id
            WHERE ua.user_id = :user_id
        ");
        $stmt->execute([':user_id' => $user_id]);
        return $stmt->fetchAll();
    }

    public function unlock(): bool {
        $stmt = $this->db->prepare("
            INSERT INTO {$this->table} (user_id, achievement_id)
            VALUES (:user_id, :achievement_id)
        ");
        return $stmt->execute([
            ':user_id'        => $this->user_id,
            ':achievement_id' => $this->achievement_id,
        ]);
    }
}
