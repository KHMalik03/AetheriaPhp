<?php

require_once __DIR__ . '/../config/db.php';
require_once __DIR__ . '/../models/achievement.model.php';
require_once __DIR__ . '/../auth/session.php';

class AchievementController {
    private AchievementModel $model;

    public function __construct() {
        $this->model = new AchievementModel();
    }

    public function readByGame(int $game_id): void {
        $achievements = $this->model->getByGameId($game_id);
        header('Content-Type: application/json');
        echo json_encode($achievements);
    }

    public function create(array $data): void {
        if (!Session::isAdmin()) {
            http_response_code(403);
            echo json_encode(['message' => 'Forbidden: admins only']);
            return;
        }

        $achievement              = new AchievementModel();
        $achievement->game_id     = (int)$data['game_id'];
        $achievement->title       = $data['title'];
        $achievement->description = $data['description'] ?? '';

        if ($achievement->create()) {
            http_response_code(201);
            echo json_encode(['message' => 'Achievement created successfully']);
        } else {
            http_response_code(500);
            echo json_encode(['message' => 'Failed to create achievement']);
        }
    }

    public function update(int $id, array $data): void {
        $achievement = $this->model->getById($id);
        if (!$achievement) {
            http_response_code(404);
            echo json_encode(['message' => 'Achievement not found']);
            return;
        }

        $achievement->title       = $data['title']       ?? $achievement->title;
        $achievement->description = $data['description'] ?? $achievement->description;

        if ($achievement->update()) {
            echo json_encode(['message' => 'Achievement updated successfully']);
        } else {
            http_response_code(500);
            echo json_encode(['message' => 'Failed to update achievement']);
        }
    }

    public function delete(int $id): void {
        $achievement = $this->model->getById($id);
        if (!$achievement) {
            http_response_code(404);
            echo json_encode(['message' => 'Achievement not found']);
            return;
        }

        if ($this->model->delete($id)) {
            echo json_encode(['message' => 'Achievement deleted successfully']);
        } else {
            http_response_code(500);
            echo json_encode(['message' => 'Failed to delete achievement']);
        }
    }
}
