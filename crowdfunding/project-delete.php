<?php

require_once 'Classe/Project.php';

// La suppression se fait seulement avec une requête POST.
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: projects.php');
    exit;
}

$id = isset($_POST['id']) ? (int) $_POST['id'] : 0;

if ($id > 0) {
    $projectModel = new Project();
    $projectModel->deleteProject($id);
}

header('Location: projects.php');
exit;
