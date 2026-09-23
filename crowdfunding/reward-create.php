<?php

require_once 'Classe/Reward.php';
require_once 'Classe/Project.php';

$projectId = isset($_GET['project_id'])
    ? (int) $_GET['project_id']
    : (int) ($_POST['project_id'] ?? 0);

$projectModel = new Project();
$rewardModel = new Reward();
$project = $projectModel->getProject($projectId);

if (!$project) {
    header('Location: projects.php');
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Si la quantité est vide, on enregistre NULL (quantité illimitée).
    $quantity = $_POST['quantity'] !== '' ? (int) $_POST['quantity'] : null;

    $data = [
        'project_id' => $projectId,
        'title' => trim($_POST['title']),
        'description' => trim($_POST['description']),
        'minimum_amount' => (float) $_POST['minimum_amount'],
        'quantity' => $quantity
    ];

    $rewardModel->createReward($data);

    header("Location: project.php?id=$projectId");
    exit;
}

require_once 'includes/header.php';
?>

<main class="container form-page">
    <div class="form-heading">
                <h1>Add a reward</h1>
        <p><?= htmlspecialchars($project['title']) ?></p>
    </div>

    <form class="main-form" method="post">
        <input type="hidden" name="project_id" value="<?= $projectId ?>">

        <label for="title">Reward title</label>
        <input type="text" name="title" id="title" maxlength="150" required>

        <label for="description">Description</label>
        <textarea name="description" id="description"></textarea>

        <label for="minimum_amount">Minimum pledge</label>
        <input type="number" name="minimum_amount" id="minimum_amount" step="0.01" min="1" required>

        <label for="quantity">Quantity</label>
        <input type="number" name="quantity" id="quantity" min="1" placeholder="Leave empty for unlimited">

        <button class="button button-dark" type="submit">Add reward</button>
    </form>
</main>

<?php require_once 'includes/footer.php'; ?>
