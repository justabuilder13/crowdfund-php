<?php

require_once __DIR__ . '/CRUD.php';

// Classe représentant l'entité User.
class User extends CRUD
{
    private string $table = 'users';

    // READ - Afficher tous les utilisateurs.
    public function getAllUsers(): array
    {
        return $this->select($this->table, 'name');
    }

    // READ - Afficher un utilisateur selon son ID.
    public function getUser(int $id)
    {
        return $this->selectId($this->table, $id);
    }

    // CREATE - Créer un utilisateur avec un mot de passe sécurisé.
    public function createUser(array $data): int
    {
        $data['password'] = password_hash($data['password'], PASSWORD_DEFAULT);

        return $this->insert($this->table, $data);
    }

    // READ - Afficher les projets créés par un utilisateur.
    public function getUserProjects(int $userId): array
    {
        $sql = "SELECT *
                FROM projects
                WHERE user_id = :user_id
                ORDER BY created_date DESC";

        $stmt = $this->prepare($sql);
        $stmt->bindValue(':user_id', $userId, PDO::PARAM_INT);
        $stmt->execute();

        return $stmt->fetchAll();
    }
}
