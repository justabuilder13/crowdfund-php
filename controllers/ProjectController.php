<?php

namespace App\Controllers;

use App\Models\Category;
use App\Models\Comment;
use App\Models\Pledge;
use App\Models\Project;
use App\Models\Reward;
use App\Models\User;
use App\Providers\Image;
use App\Providers\Validator;
use App\Providers\View;

// CRUD complet sur la table projects (clés étrangères user_id et category_id)
class ProjectController
{
    private const STATUSES = ['active', 'inactive'];

    // READ - Liste des projets, avec un filtre par catégorie optionnel.
    // Sans filtre, le projet actif le plus récent est mis en vedette avec 4 recommandations.
    public function index(array $get = []): void
    {
        $categoryId = isset($get['category']) ? (int) $get['category'] : null;

        $projectModel = new Project();
        $categoryModel = new Category();
        $projects = Image::addToProjects($projectModel->getAll($categoryId));

        $featured = null;
        $recommended = [];
        if (!$categoryId) {
            $active = array_values(array_filter(
                $projects,
                fn(array $project) => $project['status'] === 'active'
            ));
            $featured = $active[0] ?? null;
            $recommended = array_slice($active, 1, 4);
        }

        View::render('project/index', [
            'projects' => $projects,
            'featured' => $featured,
            'recommended' => $recommended,
            'categories' => $categoryModel->select('name'),
            'currentCategory' => $categoryId,
        ]);
    }

    // READ - Un projet avec ses récompenses, contributions et commentaires.
    public function show(array $get = []): void
    {
        $project = $this->findProject($get);
        if (!$project) {
            return;
        }

        $id = (int) $project['id'];
        $project['image'] = Image::project($id);

        View::render('project/show', [
            'project' => $project,
            'rewards' => (new Reward())->getByProject($id),
            'pledges' => (new Pledge())->getByProject($id),
            'comments' => (new Comment())->getByProject($id),
            'users' => (new User())->select('name'),
        ]);
    }

    // CREATE - Affiche le formulaire vide.
    public function create(): void
    {
        View::render('project/create', $this->formLists());
    }

    // CREATE - Valide et enregistre le nouveau projet.
    public function store(array $data): void
    {
        $val = $this->validate($data, true);

        if (!$val->isSuccess()) {
            View::render('project/create', $this->formLists() + [
                'errors' => $val->getErrors(),
                'project' => $data,
            ]);
            return;
        }

        $projectModel = new Project();
        $id = $projectModel->insert([
            'user_id' => (int) $data['user_id'],
            'category_id' => (int) $data['category_id'],
            'title' => trim($data['title']),
            'description' => trim($data['description']),
            'goal_amount' => (float) $data['goal_amount'],
            'start_date' => $data['start_date'],
            'end_date' => $data['end_date'],
            'status' => 'active',
        ]);

        if ($id) {
            View::redirect('project/show?id=' . $id, 'Your project has been launched.');
        }
        View::render('error', ['msg' => 'The project could not be saved.']);
    }

    // UPDATE - Affiche le formulaire rempli avec les valeurs actuelles.
    public function edit(array $get = []): void
    {
        $project = $this->findProject($get);
        if (!$project) {
            return;
        }

        View::render('project/edit', $this->formLists() + [
            'project' => $project,
            'statuses' => self::STATUSES,
        ]);
    }

    // UPDATE - Valide et enregistre les modifications.
    public function update(array $data, array $get = []): void
    {
        $project = $this->findProject($get);
        if (!$project) {
            return;
        }

        $val = $this->validate($data, false);

        if (!$val->isSuccess()) {
            // On garde l'id et les noms pour pouvoir réafficher le formulaire.
            View::render('project/edit', $this->formLists() + [
                'errors' => $val->getErrors(),
                'project' => array_merge($project, $data),
                'statuses' => self::STATUSES,
            ]);
            return;
        }

        $projectModel = new Project();
        $updated = $projectModel->update([
            'category_id' => (int) $data['category_id'],
            'title' => trim($data['title']),
            'description' => trim($data['description']),
            'goal_amount' => (float) $data['goal_amount'],
            'start_date' => $data['start_date'],
            'end_date' => $data['end_date'],
            'status' => $data['status'],
        ], $project['id']);

        if ($updated) {
            View::redirect('project/show?id=' . $project['id'], 'Changes saved.');
        }
        View::render('error', ['msg' => 'The project could not be updated.']);
    }

    // DELETE - Supprime le projet (récompenses, contributions et commentaires suivent avec ON DELETE CASCADE).
    public function delete(array $data): void
    {
        $project = $this->findProject($data);
        if (!$project) {
            return;
        }

        $projectModel = new Project();
        if ($projectModel->delete($project['id'])) {
            View::redirect('projects', 'The project "' . $project['title'] . '" has been deleted.');
        }
        View::render('error', ['msg' => 'The project could not be deleted.']);
    }

    // Trouve le projet avec ?id=, sinon affiche une page d'erreur.
    private function findProject(array $params): array|false
    {
        $id = (int) ($params['id'] ?? 0);
        $project = $id > 0 ? (new Project())->getOne($id) : false;

        if (!$project) {
            http_response_code(404);
            View::render('error', ['msg' => 'Project not found.']);
        }

        return $project;
    }

    // Listes nécessaires aux formulaires create et edit.
    private function formLists(): array
    {
        return [
            'users' => (new User())->select('name'),
            'categories' => (new Category())->select('name'),
        ];
    }

    // Règles de validation d'un projet.
    private function validate(array $data, bool $isNew): Validator
    {
        $val = new Validator();

        if ($isNew) {
            $val->field('user_id', $data['user_id'] ?? '', 'Creator')->required()->int();
        }
        $val->field('category_id', $data['category_id'] ?? '', 'Category')->required()->int();
        $val->field('title', $data['title'] ?? '')->required()->min(3)->max(150);
        $val->field('description', $data['description'] ?? '')->required()->min(20)->max(5000);
        $val->field('goal_amount', $data['goal_amount'] ?? '', 'Funding goal')
            ->required()->number()->minValue(100)->maxValue(10000000);
        $val->field('start_date', $data['start_date'] ?? '', 'Start date')->required()->date();
        $val->field('end_date', $data['end_date'] ?? '', 'End date')
            ->required()->date()->after($data['start_date'] ?? '', 'start date');

        if (!$isNew) {
            $val->field('status', $data['status'] ?? '')->required()->in(self::STATUSES);
        }

        // Les clés étrangères doivent pointer vers des lignes existantes.
        if ($isNew && !empty($data['user_id']) && !(new User())->selectId((int) $data['user_id'])) {
            $val->addCustomError('user_id', 'This creator does not exist.');
        }
        if (!empty($data['category_id']) && !(new Category())->selectId((int) $data['category_id'])) {
            $val->addCustomError('category_id', 'This category does not exist.');
        }

        return $val;
    }
}
