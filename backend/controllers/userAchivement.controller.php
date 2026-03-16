<?php

require_once __DIR__ . '/../config/db.php';
require_once __DIR__ . '/../models/userAchivement.model.php';
require_once __DIR__ . '/../auth/session.php';

class UserAchievementController {
    private UserAchievementModel $model;

    public function __construct() {
        $this->model = new UserAchievementModel();
    }

    public function getAchievements(int $user_id): void {
        $achievements = $this->model->getByUserId($user_id);
        header('Content-Type: application/json');
        echo json_encode($achievements);
    }

    public function unlock(int $user_id, array $data): void {
        header('Content-Type: application/json');
        if (!Session::isLoggedIn()) {
            http_response_code(401);
            echo json_encode(['message' => 'Unauthorized']);
            return;
        }

        if (empty($data['achievement_id'])) {
            http_response_code(400);
            echo json_encode(['message' => 'achievement_id is required']);
            return;
        }

        $entry                 = new UserAchievementModel();
        $entry->user_id        = $user_id;
        $entry->achievement_id = (int)$data['achievement_id'];

        try {
            if ($entry->unlock()) {
                http_response_code(201);
                echo json_encode(['message' => 'Achievement unlocked successfully']);
            } else {
                http_response_code(500);
                echo json_encode(['message' => 'Failed to unlock achievement']);
            }
        } catch (\PDOException $e) {
            http_response_code(400);
            echo json_encode(['message' => $e->getMessage()]);
        }
    }
}
