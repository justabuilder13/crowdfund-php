<?php

require_once 'includes/bootstrap.php';

$user = requireLogin();
$projectId = (int) ($_GET['project_id'] ?? $_POST['project_id'] ?? 0);
$selectedRewardId = (int) ($_GET['reward_id'] ?? $_POST['reward_id'] ?? 0);

$projectModel = new Project();
$rewardModel = new Reward();
$pledgeModel = new Pledge();
$project = $projectModel->getProject($projectId);

if (!$project) {
    flash('error', 'Project not found.');
    redirect('projects.php');
}

$rewards = $rewardModel->getRewardsByProject($projectId);
$error = '';
$amount = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $selectedRewardId = (int) ($_POST['reward_id'] ?? 0);
    $amount = trim($_POST['amount'] ?? '');

    try {
        $pledgeModel->createPledge([
            'user_id' => (int) $user['id'],
            'project_id' => $projectId,
            'reward_id' => $selectedRewardId > 0 ? $selectedRewardId : null,
            'amount' => (float) $amount,
            'payment_status' => 'completed'
        ]);

        flash('success', 'Thank you for supporting this project.');
        redirect("project.php?id=$projectId");
    } catch (RuntimeException $exception) {
        $error = $exception->getMessage();
    }
}

$pageTitle = 'Back project';
require_once 'includes/header.php';
?>

<main class="container form-page pledge-page">
    <div class="form-heading">
        <p class="eyebrow">Support</p>
        <h1>Back this project</h1>
        <p><?= e($project['title']) ?></p>
    </div>

    <?php if ($error): ?>
        <p class="form-error"><?= e($error) ?></p>
    <?php endif; ?>

    <form class="main-form" method="post">
        <input type="hidden" name="project_id" value="<?= $projectId ?>">

        <label for="reward_id">Reward</label>
        <select id="reward_id" name="reward_id">
            <option value="0">No reward, just support the project</option>
            <?php foreach ($rewards as $reward): ?>
                <?php if ($reward['quantity'] === null || (int) $reward['quantity'] > 0): ?>
                    <option value="<?= $reward['id'] ?>" <?= $selectedRewardId === (int) $reward['id'] ? 'selected' : '' ?>>
                        <?= e($reward['title']) ?> — $<?= number_format((float) $reward['minimum_amount'], 2) ?>+
                    </option>
                <?php endif; ?>
            <?php endforeach; ?>
        </select>

        <label for="amount">Pledge amount</label>
        <input type="number" id="amount" name="amount" min="1" step="0.01" value="<?= e($amount) ?>" required>

        <p class="form-note">This school project simulates a completed contribution. No real payment is processed.</p>

        <button class="button button-green" type="submit">Confirm pledge</button>
    </form>
</main>

<?php require_once 'includes/footer.php'; ?>
