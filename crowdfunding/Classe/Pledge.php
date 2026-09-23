<?php

require_once __DIR__ . '/CRUD.php';

class Pledge extends CRUD
{
    private string $table = 'pledges';

    public function getPledgesByProject(int $projectId): array
    {
        $sql = "SELECT pledges.*, users.name AS user_name, rewards.title AS reward_title
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

    public function countBackers(int $projectId): int
    {
        $stmt = $this->prepare('SELECT COUNT(DISTINCT user_id) FROM pledges WHERE project_id = :project_id');
        $stmt->bindValue(':project_id', $projectId, PDO::PARAM_INT);
        $stmt->execute();
        return (int) $stmt->fetchColumn();
    }

    public function createPledge(array $data): int
    {
        $projectId = (int) $data['project_id'];
        $rewardId = !empty($data['reward_id']) ? (int) $data['reward_id'] : null;
        $amount = (float) $data['amount'];

        $projectStmt = $this->prepare('SELECT status, end_date FROM projects WHERE id = :id');
        $projectStmt->bindValue(':id', $projectId, PDO::PARAM_INT);
        $projectStmt->execute();
        $project = $projectStmt->fetch();

        if (!$project || $project['status'] !== 'active' || $project['end_date'] < date('Y-m-d')) {
            throw new RuntimeException('This project is not accepting pledges.');
        }

        if ($amount <= 0) {
            throw new RuntimeException('The pledge amount must be greater than $0.');
        }

        if ($rewardId !== null) {
            $rewardStmt = $this->prepare('SELECT * FROM rewards WHERE id = :id AND project_id = :project_id');
            $rewardStmt->bindValue(':id', $rewardId, PDO::PARAM_INT);
            $rewardStmt->bindValue(':project_id', $projectId, PDO::PARAM_INT);
            $rewardStmt->execute();
            $reward = $rewardStmt->fetch();

            if (!$reward) {
                throw new RuntimeException('The selected reward is not available.');
            }

            if ($amount < (float) $reward['minimum_amount']) {
                throw new RuntimeException('The pledge amount is below the minimum for this reward.');
            }

            if ($reward['quantity'] !== null && (int) $reward['quantity'] <= 0) {
                throw new RuntimeException('This reward is sold out.');
            }
        }

        try {
            $this->beginTransaction();

            $data['reward_id'] = $rewardId;
            $pledgeId = $this->insert($this->table, $data);

            $stmt = $this->prepare('UPDATE projects SET current_amount = current_amount + :amount WHERE id = :project_id');
            $stmt->bindValue(':amount', $amount);
            $stmt->bindValue(':project_id', $projectId, PDO::PARAM_INT);
            $stmt->execute();

            if ($rewardId !== null) {
                $stmt = $this->prepare('UPDATE rewards SET quantity = quantity - 1 WHERE id = :id AND quantity IS NOT NULL');
                $stmt->bindValue(':id', $rewardId, PDO::PARAM_INT);
                $stmt->execute();
            }

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
