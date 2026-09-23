<?php

require_once 'includes/bootstrap.php';

$user = requireLogin();

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    redirect('projects.php');
}

$id = (int) ($_POST['id'] ?? 0);
$rewardModel = new Reward();
$projectModel = new Project();
$reward = $rewardModel->getReward($id);

if (!$reward) {
    redirect('projects.php');
}

$project = $projectModel->getProject((int) $reward['project_id']);

if (!$project || (int) $project['user_id'] !== (int) $user['id']) {
    flash('error', 'You cannot delete this reward.');
    redirect('projects.php');
}

deleteUploadedImage($reward['image']);
$rewardModel->deleteReward($id);
flash('success', 'Reward deleted.');
redirect('project.php?id=' . $project['id'] . '#rewards');
