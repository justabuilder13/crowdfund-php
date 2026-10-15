<?php

namespace App\Models;

// Modèle de la table pledges (relation N-N entre users et projects).
// Clés étrangères: user_id, project_id, reward_id (peut être NULL)
class Pledge extends CRUD
{
    protected string $table = 'pledges';
    protected array $fillable = ['user_id', 'project_id', 'reward_id', 'amount', 'payment_status'];

    // READ - Contributions d'un projet avec le nom du contributeur et la récompense.
    public function getByProject(int $projectId): array
    {
        $sql = "SELECT pledges.*, users.name AS user_name, rewards.title AS reward_title
                FROM pledges
                INNER JOIN users ON pledges.user_id = users.id
                LEFT JOIN rewards ON pledges.reward_id = rewards.id
                WHERE pledges.project_id = :project_id
                ORDER BY pledges.created_date DESC";

        $stmt = $this->prepare($sql);
        $stmt->bindValue(':project_id', $projectId, \PDO::PARAM_INT);
        $stmt->execute();

        return $stmt->fetchAll();
    }

    // CREATE - Enregistre la contribution, ajoute le montant au projet et
    // retire 1 au stock de la récompense. Tout passe ou rien ne passe (transaction).
    public function createPledge(array $data): int
    {
        try {
            $this->beginTransaction();

            $pledgeId = $this->insert($data);

            $stmt = $this->prepare(
                'UPDATE projects SET current_amount = current_amount + :amount WHERE id = :project_id'
            );
            $stmt->bindValue(':amount', $data['amount']);
            $stmt->bindValue(':project_id', $data['project_id'], \PDO::PARAM_INT);
            $stmt->execute();

            if (!empty($data['reward_id'])) {
                $stmt = $this->prepare(
                    'UPDATE rewards SET quantity = quantity - 1
                     WHERE id = :reward_id AND quantity IS NOT NULL AND quantity > 0'
                );
                $stmt->bindValue(':reward_id', $data['reward_id'], \PDO::PARAM_INT);
                $stmt->execute();
            }

            $this->commit();

            return $pledgeId;
        } catch (\Throwable $error) {
            if ($this->inTransaction()) {
                $this->rollBack();
            }
            throw $error;
        }
    }
}
