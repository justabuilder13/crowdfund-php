<?php

namespace App\Models;

// Modèle de la table rewards. Clé étrangère: project_id -> projects.id
class Reward extends CRUD
{
    protected string $table = 'rewards';
    protected array $fillable = ['project_id', 'title', 'description', 'minimum_amount', 'quantity'];

    // READ - Récompenses d'un projet, de la moins chère à la plus chère.
    public function getByProject(int $projectId): array
    {
        $sql = "SELECT * FROM rewards
                WHERE project_id = :project_id
                ORDER BY minimum_amount ASC";

        $stmt = $this->prepare($sql);
        $stmt->bindValue(':project_id', $projectId, \PDO::PARAM_INT);
        $stmt->execute();

        return $stmt->fetchAll();
    }
}
