<?php

require_once __DIR__ . '/CRUD.php';

class Comment extends CRUD
{
    private string $table = 'comments';

    public function getCommentsByProject(int $projectId): array
    {
        $sql = "SELECT comments.*, users.name AS user_name
                FROM comments
                INNER JOIN users ON comments.user_id = users.id
                WHERE comments.project_id = :project_id
                ORDER BY comments.created_date DESC";

        $stmt = $this->prepare($sql);
        $stmt->bindValue(':project_id', $projectId, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll();
    }

    public function getComment(int $id)
    {
        return $this->selectId($this->table, $id);
    }

    public function createComment(array $data): int
    {
        return $this->insert($this->table, $data);
    }

    public function deleteComment(int $id): bool
    {
        return $this->delete($this->table, $id);
    }
}
