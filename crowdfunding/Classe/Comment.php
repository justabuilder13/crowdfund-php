<?php

require_once __DIR__ . '/CRUD.php';

// Classe représentant l'entité Comment.
class Comment extends CRUD
{
    private string $table = 'comments';

    // READ - Afficher les commentaires d'un projet avec le nom de l'utilisateur.
    public function getCommentsByProject(int $projectId): array
    {
        $sql = "SELECT
                    comments.*,
                    users.name AS user_name
                FROM comments
                INNER JOIN users ON comments.user_id = users.id
                WHERE comments.project_id = :project_id
                ORDER BY comments.created_date DESC";

        $stmt = $this->prepare($sql);
        $stmt->bindValue(':project_id', $projectId, PDO::PARAM_INT);
        $stmt->execute();

        return $stmt->fetchAll();
    }

    // CREATE - Ajouter un commentaire.
    public function createComment(array $data): int
    {
        return $this->insert($this->table, $data);
    }
}
