<?php

namespace App\Models;

// Modèle de la table comments.
// Clés étrangères: user_id -> users.id, project_id -> projects.id
class Comment extends CRUD
{
    protected string $table = 'comments';
    protected array $fillable = ['user_id', 'project_id', 'comment'];

    // READ - Commentaires d'un projet avec le nom de l'auteur, du plus récent au plus ancien.
    public function getByProject(int $projectId): array
    {
        $sql = "SELECT comments.*, users.name AS user_name
                FROM comments
                INNER JOIN users ON comments.user_id = users.id
                WHERE comments.project_id = :project_id
                ORDER BY comments.created_date DESC, comments.id DESC";

        $stmt = $this->prepare($sql);
        $stmt->bindValue(':project_id', $projectId, \PDO::PARAM_INT);
        $stmt->execute();

        return $stmt->fetchAll();
    }
}
