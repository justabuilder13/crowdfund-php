<?php

require_once 'Classe/Project.php';
require_once 'Classe/Reward.php';
require_once 'Classe/Comment.php';
require_once 'Classe/Pledge.php';
require_once 'Classe/User.php';

$id = (int) ($_GET['id'] ?? 0);

$projectModel = new Project();
$rewardModel = new Reward();
$commentModel = new Comment();
$pledgeModel = new Pledge();
$userModel = new User();

$project = $projectModel->getProject($id);

if (!$project) {
    header('Location: projects.php');
    exit;
}

$rewards = $rewardModel->getRewardsByProject($id);
$comments = $commentModel->getCommentsByProject($id);
$pledges = $pledgeModel->getPledgesByProject($id);
$users = $userModel->getAllUsers();

$percentage = 0;

if ($project['goal_amount'] > 0) {
    $percentage = ($project['current_amount'] / $project['goal_amount']) * 100;
}

require_once 'includes/header.php';
?>

<main class="container">

    <section class="project-detail-header">
        <div>
            <p class="project-category"><?= htmlspecialchars($project['category_name']) ?></p>
            <h1><?= htmlspecialchars($project['title']) ?></h1>
            <p class="project-creator">
                By
                <a href="profile.php?id=<?= $project['user_id'] ?>">
                    <?= htmlspecialchars($project['user_name']) ?>
                </a>
            </p>
        </div>

        <div class="project-summary">
            <strong>$<?= number_format($project['current_amount'], 2) ?></strong>
            <span>pledged of $<?= number_format($project['goal_amount'], 2) ?> goal</span>

            <progress value="<?= min($percentage, 100) ?>" max="100"></progress>

            <div class="project-info">
                <span><?= round($percentage) ?>% funded</span>
                <span><?= count($pledges) ?> pledges</span>
            </div>

            <p>Ends <?= htmlspecialchars($project['end_date']) ?></p>

            <a class="button button-green" href="pledge-create.php?project_id=<?= $project['id'] ?>">
                Back this project
            </a>
        </div>
    </section>

    <section class="project-detail-grid">
        <div class="project-main-content">
            <h2>About this project</h2>
            <p class="project-long-description">
                <?= nl2br(htmlspecialchars($project['description'])) ?>
            </p>

            <section class="comments-section">
                <h2>Comments</h2>

                <?php if (count($comments) > 0): ?>
                    <?php foreach ($comments as $comment): ?>
                        <article class="comment-card">
                            <strong><?= htmlspecialchars($comment['user_name']) ?></strong>
                            <p><?= htmlspecialchars($comment['comment']) ?></p>
                        </article>
                    <?php endforeach; ?>
                <?php else: ?>
                    <p class="muted">No comments yet.</p>
                <?php endif; ?>

                <form class="main-form compact-form" action="comment-create.php" method="post">
                    <input type="hidden" name="project_id" value="<?= $project['id'] ?>">

                    <label for="user_id">User</label>
                    <select name="user_id" id="user_id" required>
                        <?php foreach ($users as $user): ?>
                            <option value="<?= $user['id'] ?>">
                                <?= htmlspecialchars($user['name']) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>

                    <label for="comment">Comment</label>
                    <textarea name="comment" id="comment" required></textarea>

                    <button class="button button-dark" type="submit">Post comment</button>
                </form>
            </section>
        </div>

        <aside class="rewards-panel">
            <div class="rewards-heading">
                <h2>Rewards</h2>
                <a href="reward-create.php?project_id=<?= $project['id'] ?>">Add reward</a>
            </div>

            <?php if (count($rewards) > 0): ?>
                <?php foreach ($rewards as $reward): ?>
                    <article class="reward-card">
                        <p class="reward-amount">
                            Pledge $<?= number_format($reward['minimum_amount'], 2) ?> or more
                        </p>

                        <h3><?= htmlspecialchars($reward['title']) ?></h3>
                        <p><?= htmlspecialchars($reward['description']) ?></p>

                        <?php if ($reward['quantity'] !== null): ?>
                            <small><?= (int) $reward['quantity'] ?> available</small>
                        <?php endif; ?>
                    </article>
                <?php endforeach; ?>
            <?php else: ?>
                <p class="muted">No rewards have been added yet.</p>
            <?php endif; ?>
        </aside>
    </section>

    <section class="project-owner-actions">
        <a class="button button-light" href="project-edit.php?id=<?= $project['id'] ?>">Edit project</a>

        <form action="project-delete.php" method="post">
            <input type="hidden" name="id" value="<?= $project['id'] ?>">
            <button class="button button-danger" type="submit">Delete project</button>
        </form>
    </section>

</main>

<?php require_once 'includes/footer.php'; ?>
