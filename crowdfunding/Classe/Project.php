<?php

require_once __DIR__ . '/CRUD.php';

// Classe représentant l'entité Project.
class Project extends CRUD
{
    private string $table = 'projects';

    // READ - Afficher tous les projets avec leur créateur et leur catégorie.
    public function getAllProjects(): array
    {
        $sql = "SELECT
                    projects.*,
                    users.name AS user_name,
                    categories.name AS category_name
                FROM projects
                INNER JOIN users ON projects.user_id = users.id
                INNER JOIN categories ON projects.category_id = categories.id
                ORDER BY projects.created_date DESC";

        $stmt = $this->query($sql);

        return $stmt->fetchAll();
    }

    // READ - Afficher un projet selon son ID avec les relations nécessaires.
    public function getProject(int $id)
    {
        $sql = "SELECT
                    projects.*,
                    users.name AS user_name,
                    categories.name AS category_name
                FROM projects
                INNER JOIN users ON projects.user_id = users.id
                INNER JOIN categories ON projects.category_id = categories.id
                WHERE projects.id = :id";

        $stmt = $this->prepare($sql);
        $stmt->bindValue(':id', $id, PDO::PARAM_INT);
        $stmt->execute();

        return $stmt->fetch();
    }

    // CREATE - Créer un nouveau projet.
    public function createProject(array $data): int
    {
        return $this->insert($this->table, $data);
    }

    // UPDATE - Modifier un projet.
    public function updateProject(int $id, array $data): bool
    {
        return $this->update($this->table, $data, $id);
    }

    // DELETE - Supprimer un projet.
    public function deleteProject(int $id): bool
    {
        return $this->delete($this->table, $id);
    }
}
