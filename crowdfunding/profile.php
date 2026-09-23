<?php

require_once 'Classe/User.php';

$userId = (int) ($_GET['id'] ?? 1);
$userModel = new User();
$user = $userModel->getUser($userId);

if (!$user) {
    header('Location: index.php');
    exit;
}

$projects = $userModel->getUserProjects($userId);

require_once 'includes/header.php';
?>

<main class="container">

    <section class="profile-header">
                <h1><?= htmlspecialchars($user['name']) ?></h1>
        <p><?= htmlspecialchars($user['email']) ?></p>
        <p class="muted">Member since <?= htmlspecialchars($user['created_date']) ?></p>
    </section>

    <section class="profile-projects">
        <div class="section-heading">
            <h2>Projects</h2>
            <a href="project-create.php">Start a new project</a>
        </div>

        <?php if (count($projects) > 0): ?>
            <div class="projects-grid">

                <?php foreach ($projects as $project): ?>

                    <?php
                    $percentage = 0;

                    if ($project['goal_amount'] > 0) {
                        $percentage = ($project['current_amount'] / $project['goal_amount']) * 100;
                    }
                    ?>

                    <article class="project-card">
                        <h3><?= htmlspecialchars($project['title']) ?></h3>
                        <p class="project-description"><?= htmlspecialchars($project['description']) ?></p>

                        <div class="project-funding">
                            <strong>$<?= number_format($project['current_amount'], 2) ?></strong>
                            <span>of $<?= number_format($project['goal_amount'], 2) ?> goal</span>
                        </div>

                        <progress value="<?= min($percentage, 100) ?>" max="100"></progress>

                        <div class="project-actions">
                            <a href="project.php?id=<?= $project['id'] ?>">View</a>
                            <a href="project-edit.php?id=<?= $project['id'] ?>">Edit</a>
                        </div>
                    </article>

                <?php endforeach; ?>

            </div>
        <?php else: ?>
            <div class="empty-state">
                <h3>No projects yet</h3>
                <p>This creator has not created a project yet.</p>
                <a class="button button-dark" href="project-create.php">Start a project</a>
            </div>
        <?php endif; ?>
    </section>

</main>

<?php require_once 'includes/footer.php'; ?>
