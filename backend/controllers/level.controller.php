<?php

require_once __DIR__ . '/../config/db.php';
require_once __DIR__ . '/../models/level.model.php';
require_once __DIR__ . '/../auth/session.php';

class LevelController {
    private LevelModel $model;

    public function __construct() {
        $this->model = new LevelModel();
    }

    public function readGamesByDifficulty(string $difficulty): void {
        $games = $this->model->getGamesByDifficulty($difficulty);
        header('Content-Type: application/json');
        echo json_encode($games);
    }

    public function addGameToLevel(array $data): void {
        header('Content-Type: application/json');
        if (!Session::isAdmin()) {
            http_response_code(403);
            echo json_encode(['message' => 'Forbidden: admins only']);
            return;
        }

        $level              = new LevelModel();
        $level->game_id     = (int)$data['game_id'];
        $level->difficulty  = $data['difficulty'];
        $level->description = $data['description'] ?? '';

        if ($level->addGameToLevel()) {
            http_response_code(201);
            echo json_encode(['message' => 'Game added to level successfully']);
        } else {
            http_response_code(500);
            echo json_encode(['message' => 'Failed to add game to level']);
        }
    }
}
