<?php

require_once 'Classe/Pledge.php';
require_once 'Classe/Project.php';
require_once 'Classe/Reward.php';
require_once 'Classe/User.php';

$projectId = (int) ($_GET['project_id'] ?? $_POST['project_id'] ?? 0);

$projectModel = new Project();
$rewardModel = new Reward();
$userModel = new User();
$pledgeModel = new Pledge();

$project = $projectModel->getProject($projectId);

if (!$project) {
    header('Location: projects.php');
    exit;
}

$rewards = $rewardModel->getRewardsByProject($projectId);
$users = $userModel->getAllUsers();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $rewardId = !empty($_POST['reward_id']) ? (int) $_POST['reward_id'] : null;

    $data = [
        'user_id' => (int) $_POST['user_id'],
        'project_id' => $projectId,
        'reward_id' => $rewardId,
        'amount' => (float) $_POST['amount'],
        'payment_status' => 'completed'
    ];

    $pledgeModel->createPledge($data);

    header("Location: project.php?id=$projectId");
    exit;
}

require_once 'includes/header.php';
?>

<main class="container form-page">
    <div class="form-heading">
                <h1>Back this project</h1>
        <p><?= htmlspecialchars($project['title']) ?></p>
    </div>

    <form class="main-form" method="post">
        <input type="hidden" name="project_id" value="<?= $projectId ?>">

        <label for="user_id">Backer</label>
        <select name="user_id" id="user_id" required>
            <?php foreach ($users as $user): ?>
                <option value="<?= $user['id'] ?>">
                    <?= htmlspecialchars($user['name']) ?>
                </option>
            <?php endforeach; ?>
        </select>

        <label for="reward_id">Reward</label>
        <select name="reward_id" id="reward_id">
            <option value="">No reward</option>

            <?php foreach ($rewards as $reward): ?>
                <option value="<?= $reward['id'] ?>">
                    <?= htmlspecialchars($reward['title']) ?> - $<?= number_format($reward['minimum_amount'], 2) ?>
                </option>
            <?php endforeach; ?>
        </select>

        <label for="amount">Pledge amount</label>
        <input type="number" name="amount" id="amount" min="1" step="0.01" required>

        <button class="button button-green" type="submit">Back project</button>
    </form>
</main>

<?php require_once 'includes/footer.php'; ?>
