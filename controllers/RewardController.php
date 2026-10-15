<?php

namespace App\Controllers;

use App\Models\Project;
use App\Models\Reward;
use App\Providers\Validator;
use App\Providers\View;

class RewardController
{
    // CREATE - Affiche le formulaire pour ajouter une récompense.
    public function create(array $get = []): void
    {
        $project = $this->findProject($get['project_id'] ?? 0);
        if (!$project) {
            return;
        }

        View::render('reward/create', ['project' => $project]);
    }

    // CREATE - Valide et enregistre la récompense.
    public function store(array $data, array $get = []): void
    {
        $project = $this->findProject($get['project_id'] ?? 0);
        if (!$project) {
            return;
        }

        $val = new Validator();
        $val->field('title', $data['title'] ?? '')->required()->min(3)->max(150);
        $val->field('description', $data['description'] ?? '')->max(1000);
        $val->field('minimum_amount', $data['minimum_amount'] ?? '', 'Minimum pledge')
            ->required()->number()->minValue(1)->maxValue(100000);
        $val->field('quantity', $data['quantity'] ?? '')->int()->minValue(1);

        if (!$val->isSuccess()) {
            View::render('reward/create', [
                'project' => $project,
                'errors' => $val->getErrors(),
                'reward' => $data,
            ]);
            return;
        }

        $rewardModel = new Reward();
        $rewardModel->insert([
            'project_id' => $project['id'],
            'title' => trim($data['title']),
            'description' => trim($data['description'] ?? ''),
            'minimum_amount' => (float) $data['minimum_amount'],
            // Quantité vide = illimitée (NULL).
            'quantity' => ($data['quantity'] ?? '') === '' ? null : (int) $data['quantity'],
        ]);

        View::redirect('project/show?id=' . $project['id'], 'Reward added.');
    }

    // DELETE - Supprime une récompense (les contributions restent, reward_id devient NULL).
    public function delete(array $data): void
    {
        $rewardModel = new Reward();
        $reward = $rewardModel->selectId((int) ($data['id'] ?? 0));

        if (!$reward) {
            View::render('error', ['msg' => 'Reward not found.']);
            return;
        }

        $rewardModel->delete($reward['id']);
        View::redirect('project/show?id=' . $reward['project_id'], 'Reward deleted.');
    }

    private function findProject(int|string $id): array|false
    {
        $project = (int) $id > 0 ? (new Project())->getOne((int) $id) : false;

        if (!$project) {
            http_response_code(404);
            View::render('error', ['msg' => 'Project not found.']);
        }

        return $project;
    }
}
