<?php

namespace App\Controllers;

use App\Models\Pledge;
use App\Models\Project;
use App\Models\Reward;
use App\Models\User;
use App\Providers\Validator;
use App\Providers\View;

class PledgeController
{
    // CREATE - Affiche le formulaire pour contribuer au projet.
    public function create(array $get = []): void
    {
        $project = $this->findProject($get['project_id'] ?? 0);
        if (!$project) {
            return;
        }

        View::render('pledge/create', $this->formData($project) + [
            'pledge' => ['reward_id' => $get['reward_id'] ?? ''],
        ]);
    }

    // CREATE - Valide et enregistre la contribution.
    public function store(array $data, array $get = []): void
    {
        $project = $this->findProject($get['project_id'] ?? 0);
        if (!$project) {
            return;
        }

        $val = new Validator();
        $val->field('user_id', $data['user_id'] ?? '', 'Backer')->required()->int();
        $val->field('reward_id', $data['reward_id'] ?? '', 'Reward')->int();
        $val->field('amount', $data['amount'] ?? '')->required()->number()->minValue(1)->maxValue(100000);

        if ($project['status'] !== 'active') {
            $val->addCustomError('amount', 'This project is not accepting pledges.');
        }

        if (!empty($data['user_id']) && !(new User())->selectId((int) $data['user_id'])) {
            $val->addCustomError('user_id', 'This backer does not exist.');
        }

        // La récompense doit appartenir au projet, être en stock,
        // et le montant doit atteindre son minimum.
        $rewardId = !empty($data['reward_id']) ? (int) $data['reward_id'] : null;
        if ($rewardId) {
            $reward = (new Reward())->selectId($rewardId);

            if (!$reward || (int) $reward['project_id'] !== (int) $project['id']) {
                $val->addCustomError('reward_id', 'This reward is not available for this project.');
            } elseif ($reward['quantity'] !== null && (int) $reward['quantity'] <= 0) {
                $val->addCustomError('reward_id', 'This reward is sold out.');
            } elseif (is_numeric($data['amount'] ?? '') && (float) $data['amount'] < (float) $reward['minimum_amount']) {
                $val->addCustomError(
                    'amount',
                    'This reward requires a pledge of at least $' . number_format($reward['minimum_amount'], 2) . '.'
                );
            }
        }

        if (!$val->isSuccess()) {
            View::render('pledge/create', $this->formData($project) + [
                'errors' => $val->getErrors(),
                'pledge' => $data,
            ]);
            return;
        }

        $pledgeModel = new Pledge();
        $pledgeModel->createPledge([
            'user_id' => (int) $data['user_id'],
            'project_id' => (int) $project['id'],
            'reward_id' => $rewardId,
            'amount' => (float) $data['amount'],
            'payment_status' => 'completed',
        ]);

        View::redirect('project/show?id=' . $project['id'], 'Thank you for backing this project!');
    }

    private function formData(array $project): array
    {
        return [
            'project' => $project,
            'rewards' => (new Reward())->getByProject((int) $project['id']),
            'users' => (new User())->select('name'),
        ];
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
