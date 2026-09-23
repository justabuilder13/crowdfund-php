<?php

require_once 'includes/bootstrap.php';

$user = requireLogin();

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    redirect('projects.php');
}

$id = (int) ($_POST['id'] ?? 0);
$commentModel = new Comment();
$projectModel = new Project();
$comment = $commentModel->getComment($id);

if (!$comment) {
    redirect('projects.php');
}

$project = $projectModel->getProject((int) $comment['project_id']);
$isCommentOwner = (int) $comment['user_id'] === (int) $user['id'];
$isProjectOwner = $project && (int) $project['user_id'] === (int) $user['id'];

if (!$isCommentOwner && !$isProjectOwner) {
    flash('error', 'You cannot delete this comment.');
    redirect('project.php?id=' . $comment['project_id'] . '#comments');
}

$commentModel->deleteComment($id);
flash('success', 'Comment deleted.');
redirect('project.php?id=' . $comment['project_id'] . '#comments');
