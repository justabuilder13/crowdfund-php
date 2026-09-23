<?php

require_once __DIR__ . '/CRUD.php';

// Classe représentant l'entité Reward.
class Reward extends CRUD
{
    private string $table = 'rewards';

    // READ - Afficher les récompenses d'un projet.
    public function getRewardsByProject(int $projectId): array
    {
        $sql = "SELECT *
                FROM rewards
                WHERE project_id = :project_id
                ORDER BY minimum_amount ASC";

        $stmt = $this->prepare($sql);
        $stmt->bindValue(':project_id', $projectId, PDO::PARAM_INT);
        $stmt->execute();

        return $stmt->fetchAll();
    }

    // READ - Afficher une récompense selon son ID.
    public function getReward(int $id)
    {
        return $this->selectId($this->table, $id);
    }

    // CREATE - Ajouter une récompense.
    public function createReward(array $data): int
    {
        return $this->insert($this->table, $data);
    }
}
