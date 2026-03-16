<?php

require_once __DIR__ . '/../config/db.php';
require_once __DIR__ . '/../models/userGame.model.php';
require_once __DIR__ . '/../auth/session.php';

class UserGameController {
    private UserGameModel $model;

    public function __construct() {
        $this->model = new UserGameModel();
    }

    public function getGames(int $user_id): void {
        $games = $this->model->getByUserId($user_id);
        header('Content-Type: application/json');
        echo json_encode($games);
    }

    public function addGame(int $user_id, array $data): void {
        header('Content-Type: application/json');
        if (!Session::isLoggedIn()) {
            http_response_code(401);
            echo json_encode(['message' => 'Unauthorized']);
            return;
        }

        $entry          = new UserGameModel();
        $entry->user_id = $user_id;
        $entry->game_id = (int)$data['game_id'];

        if ($entry->add()) {
            http_response_code(201);
            echo json_encode(['message' => 'Game added to user successfully']);
        } else {
            http_response_code(500);
            echo json_encode(['message' => 'Failed to add game']);
        }
    }

    public function removeGame(int $user_id, int $game_id): void {
        header('Content-Type: application/json');
        if (!Session::isLoggedIn()) {
            http_response_code(401);
            echo json_encode(['message' => 'Unauthorized']);
            return;
        }

        if ($this->model->remove($user_id, $game_id)) {
            echo json_encode(['message' => 'Game removed from user successfully']);
        } else {
            http_response_code(500);
            echo json_encode(['message' => 'Failed to remove game']);
        }
    }
}
