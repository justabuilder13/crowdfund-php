<?php

namespace App\Controllers;

use App\Models\Comment;
use App\Models\Project;
use App\Models\User;
use App\Providers\Validator;
use App\Providers\View;

class CommentController
{
    // CREATE - Ajoute un commentaire sous un projet.
    public function store(array $data): void
    {
        $projectId = (int) ($data['project_id'] ?? 0);

        if (!(new Project())->selectId($projectId)) {
            View::render('error', ['msg' => 'Project not found.']);
            return;
        }

        $val = new Validator();
        $val->field('user_id', $data['user_id'] ?? '', 'User')->required()->int();
        $val->field('comment', $data['comment'] ?? '')->required()->min(2)->max(1000);

        if (!empty($data['user_id']) && !(new User())->selectId((int) $data['user_id'])) {
            $val->addCustomError('user_id', 'This user does not exist.');
        }

        if (!$val->isSuccess()) {
            // Affiche la première erreur comme message flash sur la page du projet.
            $errors = $val->getErrors();
            View::redirect('project/show?id=' . $projectId . '#comments', reset($errors), 'error');
        }

        (new Comment())->insert([
            'user_id' => (int) $data['user_id'],
            'project_id' => $projectId,
            'comment' => trim($data['comment']),
        ]);

        View::redirect('project/show?id=' . $projectId . '#comments', 'Comment posted.');
    }
}
