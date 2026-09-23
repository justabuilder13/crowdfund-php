<?php

require_once __DIR__ . '/CRUD.php';

class Reward extends CRUD
{
    private string $table = 'rewards';

    public function getRewardsByProject(int $projectId): array
    {
        $stmt = $this->prepare('SELECT * FROM rewards WHERE project_id = :project_id ORDER BY minimum_amount ASC');
        $stmt->bindValue(':project_id', $projectId, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll();
    }

    public function getReward(int $id)
    {
        return $this->selectId($this->table, $id);
    }

    public function createReward(array $data): int
    {
        return $this->insert($this->table, $data);
    }

    public function updateReward(int $id, array $data): bool
    {
        return $this->update($this->table, $data, $id);
    }

    public function deleteReward(int $id): bool
    {
        return $this->delete($this->table, $id);
    }
}
