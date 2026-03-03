<?php

class GameModel {
    //Table name
    private string $table = 'game';

    //Attributes
    private int $id;
    private string $name;
    private string $type;
    private string $description;
    private string $release_date;
    private string $studio;
    private string $image_url;
    private string $created_at;
    private string $updated_at;

     //connect to db
    private PDO $db;

    public function __construct() {
        $this->db = Database::connect();
    }

    public function hydrate(array $row): self{
        $this->id = (int)$row['id'];
        $this->name = $row['name'];
        $this->type = $row['type'];
        $this->description = $row['description'];
        $this->release_date = $row['release_date'];
        $this->studio = $row['studio'];
        $this->image_url = $row['image_url'];
        $this->created_at = $row['created_at'];
        $this->updated_at = $row['updated_at'];

        return $this;
    } 

    //CRUD operations
    public function create(): bool {
        $stmt = $this->db->prepare("INSERT INTO {$this->table} (name, type, description, release_date, studio, image_url) VALUES (:name, :type, :description, :release_date, :studio, :image_url)");
        return $stmt->execute([
            ':name' => $this->name,
            ':type' => $this->type,
            ':description' => $this->description,
            ':release_date' => $this->release_date,
            ':studio' => $this->studio,
            ':image_url' => $this->image_url
        ]);
    }

    public function getGameById(int $id): ?self {
        $stmt = $this->db->prepare("SELECT * FROM {$this->table} WHERE id = :id");
        $stmt->execute([':id' => $id]);
        $row = $stmt->fetch();
        return $row ? $this->hydrate($row) : null;
    }

    public function getAllGames(): array {
        $stmt = $this->db->query("SELECT * FROM {$this->table}");
        $games = [];
        while ($row = $stmt->fetch()) {
            $games[] = (new self())->hydrate($row);
        }
        return $games;
    }

    public function updateGame(): bool {
        $stmt = $this->db->prepare("UPDATE {$this->table} SET name = :name, type = :type, description = :description, release_date = :release_date, studio = :studio, image_url = :image_url WHERE id = :id");
        return $stmt->execute([
            ':name' => $this->name,
            ':type' => $this->type,
            ':description' => $this->description,
            ':release_date' => $this->release_date,
            ':studio' => $this->studio,
            ':image_url' => $this->image_url,
            ':id' => $this->id
        ]);
    }

    public function deleteGame(int $id): bool {
        $stmt = $this->db->prepare("DELETE FROM {$this->table} WHERE id = :id");
        return $stmt->execute([':id' => $id]);
    }

}