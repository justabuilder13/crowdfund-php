<?php

require_once 'includes/bootstrap.php';

$user = requireLogin();

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    redirect('projects.php');
}

$projectId = (int) ($_POST['project_id'] ?? 0);
$comment = trim($_POST['comment'] ?? '');
$projectModel = new Project();

if (!$projectModel->getProject($projectId)) {
    flash('error', 'Project not found.');
    redirect('projects.php');
}

if ($comment === '') {
    flash('error', 'The comment cannot be empty.');
    redirect("project.php?id=$projectId#comments");
}

$commentModel = new Comment();
$commentModel->createComment([
    'user_id' => (int) $user['id'],
    'project_id' => $projectId,
    'comment' => $comment
]);

flash('success', 'Comment posted.');
redirect("project.php?id=$projectId#comments");
