<?php

namespace App\Models;

// Modèle de la table users (créateurs et contributeurs).
class User extends CRUD
{
    protected string $table = 'users';
    protected array $fillable = ['name', 'email', 'password'];

    // READ - Projets créés par un utilisateur, avec progression et catégorie.
    public function getProjects(int $userId): array
    {
        $sql = "SELECT
                    projects.*,
                    categories.name AS category_name,
                    ROUND(projects.current_amount / projects.goal_amount * 100) AS percentage
                FROM projects
                INNER JOIN categories ON projects.category_id = categories.id
                WHERE projects.user_id = :user_id
                ORDER BY projects.created_date DESC";

        $stmt = $this->prepare($sql);
        $stmt->bindValue(':user_id', $userId, \PDO::PARAM_INT);
        $stmt->execute();

        return $stmt->fetchAll();
    }

    // READ - Contributions d'un utilisateur, avec le titre du projet.
    public function getPledges(int $userId): array
    {
        $sql = "SELECT
                    pledges.*,
                    projects.title AS project_title
                FROM pledges
                INNER JOIN projects ON pledges.project_id = projects.id
                WHERE pledges.user_id = :user_id
                ORDER BY pledges.created_date DESC";

        $stmt = $this->prepare($sql);
        $stmt->bindValue(':user_id', $userId, \PDO::PARAM_INT);
        $stmt->execute();

        return $stmt->fetchAll();
    }
}
