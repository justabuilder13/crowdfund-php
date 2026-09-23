<?php

require_once __DIR__ . '/CRUD.php';

class Project extends CRUD
{
    private string $table = 'projects';

    public function refreshExpiredStatuses(): void
    {
        $this->exec("UPDATE projects SET status = 'closed' WHERE status = 'active' AND end_date < CURDATE()");
    }

    public function getAllProjects(string $search = '', int $categoryId = 0): array
    {
        $this->refreshExpiredStatuses();

        $sql = "SELECT projects.*, users.name AS user_name, categories.name AS category_name
                FROM projects
                INNER JOIN users ON projects.user_id = users.id
                INNER JOIN categories ON projects.category_id = categories.id
                WHERE projects.status = 'active'";

        $params = [];

        if ($search !== '') {
            $sql .= " AND (projects.title LIKE :search OR projects.description LIKE :search)";
            $params['search'] = '%' . $search . '%';
        }

        if ($categoryId > 0) {
            $sql .= " AND projects.category_id = :category_id";
            $params['category_id'] = $categoryId;
        }

        $sql .= ' ORDER BY projects.created_date DESC';
        $stmt = $this->prepare($sql);

        foreach ($params as $key => $value) {
            $stmt->bindValue(":$key", $value);
        }

        $stmt->execute();
        return $stmt->fetchAll();
    }

    public function getFeaturedProjects(int $limit = 3): array
    {
        $this->refreshExpiredStatuses();

        $sql = "SELECT projects.*, users.name AS user_name, categories.name AS category_name
                FROM projects
                INNER JOIN users ON projects.user_id = users.id
                INNER JOIN categories ON projects.category_id = categories.id
                WHERE projects.status = 'active'
                ORDER BY projects.current_amount DESC, projects.created_date DESC
                LIMIT :limit";

        $stmt = $this->prepare($sql);
        $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll();
    }

    public function getProject(int $id)
    {
        $this->refreshExpiredStatuses();

        $sql = "SELECT projects.*, users.name AS user_name, users.email AS user_email, categories.name AS category_name
                FROM projects
                INNER JOIN users ON projects.user_id = users.id
                INNER JOIN categories ON projects.category_id = categories.id
                WHERE projects.id = :id";

        $stmt = $this->prepare($sql);
        $stmt->bindValue(':id', $id, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetch();
    }

    public function createProject(array $data): int
    {
        return $this->insert($this->table, $data);
    }

    public function updateProject(int $id, array $data): bool
    {
        return $this->update($this->table, $data, $id);
    }

    public function deleteProject(int $id): bool
    {
        return $this->delete($this->table, $id);
    }
}
