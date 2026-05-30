<?php
require_once __DIR__ . '/../config/database.php';

class DestinationModel
{
    private PDO $pdo;

    public function __construct()
    {
        $this->pdo = getDB();
    }

    public function getAll(): array
    {
        $stmt = $this->pdo->query(
            'SELECT id_destination, nom, pays, region, description, image_url
             FROM destination
             ORDER BY nom'
        );
        return $stmt->fetchAll();
    }

    public function getById(int $id): array|false
    {
        $stmt = $this->pdo->prepare(
            'SELECT id_destination, nom, pays, region, description, image_url
             FROM destination
             WHERE id_destination = ?'
        );
        $stmt->execute([$id]);
        return $stmt->fetch();
    }
}
