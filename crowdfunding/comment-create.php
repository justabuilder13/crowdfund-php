<?php

require_once 'Classe/Comment.php';

// Les commentaires sont ajoutés uniquement avec une requête POST.
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: projects.php');
    exit;
}

$projectId = (int) ($_POST['project_id'] ?? 0);
$userId = (int) ($_POST['user_id'] ?? 0);
$commentText = trim($_POST['comment'] ?? '');

if ($projectId > 0 && $userId > 0 && $commentText !== '') {
    $commentModel = new Comment();

    $commentModel->createComment([
        'user_id' => $userId,
        'project_id' => $projectId,
        'comment' => $commentText
    ]);
}

header("Location: project.php?id=$projectId");
exit;
