<?php

namespace App\Models;

// Modèle de la table projects.
// Clés étrangères: user_id -> users.id, category_id -> categories.id
class Project extends CRUD
{
    protected string $table = 'projects';
    protected array $fillable = [
        'user_id',
        'category_id',
        'title',
        'description',
        'goal_amount',
        'start_date',
        'end_date',
        'status',
    ];

    // SELECT commun avec le nom du créateur, la catégorie et le % financé.
    private string $baseQuery = "SELECT
            projects.*,
            users.name AS user_name,
            categories.name AS category_name,
            ROUND(projects.current_amount / projects.goal_amount * 100) AS percentage
        FROM projects
        INNER JOIN users ON projects.user_id = users.id
        INNER JOIN categories ON projects.category_id = categories.id";

    // READ - Tous les projets (ou une seule catégorie), du plus récent au plus ancien.
    public function getAll(?int $categoryId = null): array
    {
        $sql = $this->baseQuery;

        if ($categoryId) {
            $sql .= ' WHERE projects.category_id = :category_id';
        }
        $sql .= ' ORDER BY projects.created_date DESC, projects.id DESC';

        $stmt = $this->prepare($sql);
        if ($categoryId) {
            $stmt->bindValue(':category_id', $categoryId, \PDO::PARAM_INT);
        }
        $stmt->execute();

        return $stmt->fetchAll();
    }

    // READ - Un projet avec ses relations.
    public function getOne(int $id): array|false
    {
        $stmt = $this->prepare($this->baseQuery . ' WHERE projects.id = :id');
        $stmt->bindValue(':id', $id, \PDO::PARAM_INT);
        $stmt->execute();

        return $stmt->fetch();
    }
}
