<?php

namespace App\Controllers;

use App\Models\User;
use App\Providers\View;

class UserController
{
    // READ - Profil d'un utilisateur: projets créés et contributions.
    public function show(array $get = []): void
    {
        $id = (int) ($get['id'] ?? 0);
        $userModel = new User();
        $user = $id > 0 ? $userModel->selectId($id) : false;

        if (!$user) {
            http_response_code(404);
            View::render('error', ['msg' => 'User not found.']);
            return;
        }

        View::render('user/show', [
            'user' => $user,
            'projects' => $userModel->getProjects($id),
            'pledges' => $userModel->getPledges($id),
        ]);
    }
}
