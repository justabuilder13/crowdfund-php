<?php

require_once 'includes/bootstrap.php';

$user = requireLogin();

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    redirect('projects.php');
}

$id = (int) ($_POST['id'] ?? 0);
$projectModel = new Project();
$rewardModel = new Reward();
$project = $projectModel->getProject($id);

if (!$project || (int) $project['user_id'] !== (int) $user['id']) {
    flash('error', 'You cannot delete this project.');
    redirect('projects.php');
}

deleteUploadedImage($project['image']);

foreach ($rewardModel->getRewardsByProject($id) as $reward) {
    deleteUploadedImage($reward['image']);
}

$projectModel->deleteProject($id);

flash('success', 'Project deleted.');
redirect('profile.php?id=' . $user['id']);
