<?php

require_once __DIR__ . '/CRUD.php';

// Classe représentant l'entité Pledge.
class Pledge extends CRUD
{
    private string $table = 'pledges';

    // READ - Afficher les contributions d'un projet.
    public function getPledgesByProject(int $projectId): array
    {
        $sql = "SELECT
                    pledges.*,
                    users.name AS user_name,
                    rewards.title AS reward_title
                FROM pledges
                INNER JOIN users ON pledges.user_id = users.id
                LEFT JOIN rewards ON pledges.reward_id = rewards.id
                WHERE pledges.project_id = :project_id
                ORDER BY pledges.created_date DESC";

        $stmt = $this->prepare($sql);
        $stmt->bindValue(':project_id', $projectId, PDO::PARAM_INT);
        $stmt->execute();

        return $stmt->fetchAll();
    }

    // CREATE - Ajouter une contribution et mettre à jour le montant du projet.
    public function createPledge(array $data): int
    {
        try {
            $this->beginTransaction();

            $pledgeId = $this->insert($this->table, $data);

            $sql = "UPDATE projects
                    SET current_amount = current_amount + :amount
                    WHERE id = :project_id";

            $stmt = $this->prepare($sql);
            $stmt->bindValue(':amount', $data['amount']);
            $stmt->bindValue(':project_id', $data['project_id'], PDO::PARAM_INT);
            $stmt->execute();

            $this->commit();

            return $pledgeId;
        } catch (Throwable $error) {
            if ($this->inTransaction()) {
                $this->rollBack();
            }

            throw $error;
        }
    }
}
