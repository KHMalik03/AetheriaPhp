<?php

require_once __DIR__ . '/../config/db.php';
require_once __DIR__ . '/../models/game.model.php';

class GameController {
    private GameModel $gameModel;

    public function __construct() {
        $this->gameModel = new GameModel(Database::connect());
    }

    public function read():void {
        $games = $this->gameModel->getAllGames();
        header('Content-Type: application/json');
        echo json_encode($games);
    }

    public function readOne(int $id): void {
        $game = $this->gameModel->getGameById($id);
        if ($game) {
            header('Content-Type: application/json');
            echo json_encode($game);
        } else {
            http_response_code(404);
            echo json_encode(['message' => 'Game not found']);
        }
    }

    public function create(array $data): void {
        $game = new GameModel();
        $game->name = $data['name'];
        $game->type = $data['type'];
        $game->description = $data['description'];
        $game->release_date = $data['release_date'];
        $game->studio = $data['studio'];
        $game->image_url = $data['image_url'];

        if ($game->create()) {
            http_response_code(201);
            echo json_encode(['message' => 'Game created successfully']);
        } else {
            http_response_code(500);
            echo json_encode(['message' => 'Failed to create game']);
        }
    }

    public function update(int $id, array $data): void {
        $game = $this->gameModel->getGameById($id);
        if (!$game) {
            http_response_code(404);
            echo json_encode(['message' => 'Game not found']);
            return;
        }

        $game->name = $data['name'] ?? $game->name;
        $game->type = $data['type'] ?? $game->type;
        $game->description = $data['description'] ?? $game->description;
        $game->release_date = $data['release_date'] ?? $game->release_date;
        $game->studio = $data['studio'] ?? $game->studio;
        $game->image_url = $data['image_url'] ?? $game->image_url;

        if ($game->updateGame()) {
            echo json_encode(['message' => 'Game updated successfully']);
        } else {
            http_response_code(500);
            echo json_encode(['message' => 'Failed to update game']);
        }
    }

   public function delete(int $id): void {
        $game = $this->gameModel->getGameById($id);
        if (!$game) {
            http_response_code(404);
            echo json_encode(['message' => 'Game not found']);
            return;
        }

        if ($this->gameModel->deleteGame($id)) {
            echo json_encode(['message' => 'Game deleted successfully']);
        } else {
            http_response_code(500);
            echo json_encode(['message' => 'Failed to delete game']);
        }
    }
}