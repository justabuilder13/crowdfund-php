<?php

require_once __DIR__ . '/CRUD.php';

class User extends CRUD
{
    private string $table = 'users';

    public function getAllUsers(): array
    {
        return $this->select($this->table, 'name');
    }

    public function getUser(int $id)
    {
        return $this->selectId($this->table, $id);
    }

    public function getUserByEmail(string $email)
    {
        $stmt = $this->prepare('SELECT * FROM users WHERE email = :email LIMIT 1');
        $stmt->bindValue(':email', $email);
        $stmt->execute();
        return $stmt->fetch();
    }

    public function createUser(array $data): int
    {
        $data['password'] = password_hash($data['password'], PASSWORD_DEFAULT);
        return $this->insert($this->table, $data);
    }

    public function authenticate(string $email, string $password)
    {
        $user = $this->getUserByEmail($email);

        if (!$user || !password_verify($password, $user['password'])) {
            return false;
        }

        return $user;
    }

    public function getUserProjects(int $userId): array
    {
        $sql = "SELECT projects.*, categories.name AS category_name
                FROM projects
                INNER JOIN categories ON projects.category_id = categories.id
                WHERE projects.user_id = :user_id
                ORDER BY projects.created_date DESC";

        $stmt = $this->prepare($sql);
        $stmt->bindValue(':user_id', $userId, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll();
    }

    public function getUserPledges(int $userId): array
    {
        $sql = "SELECT pledges.*, projects.title AS project_title
                FROM pledges
                INNER JOIN projects ON pledges.project_id = projects.id
                WHERE pledges.user_id = :user_id
                ORDER BY pledges.created_date DESC";

        $stmt = $this->prepare($sql);
        $stmt->bindValue(':user_id', $userId, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll();
    }
}
