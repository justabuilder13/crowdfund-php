<?php

require_once 'Classe/Project.php';

$projectModel = new Project();
$projects = $projectModel->getAllProjects();

require_once 'includes/header.php';
?>

<main class="container">

    <section class="projects-header">
        <div>
                        <h1>Explore projects</h1>
            <p>Browse crowdfunding campaigns created by our community.</p>
        </div>

        <a class="button button-dark" href="project-create.php">Start a project</a>
    </section>

    <?php if (count($projects) > 0): ?>

        <section class="projects-grid">

            <?php foreach ($projects as $project): ?>

                <?php
                $percentage = 0;

                if ($project['goal_amount'] > 0) {
                    $percentage = ($project['current_amount'] / $project['goal_amount']) * 100;
                }
                ?>

                <article class="project-card">
                    <p class="project-category">
                        <?= htmlspecialchars($project['category_name']) ?>
                    </p>

                    <h2><?= htmlspecialchars($project['title']) ?></h2>

                    <p class="project-creator">
                        By <?= htmlspecialchars($project['user_name']) ?>
                    </p>

                    <p class="project-description">
                        <?= htmlspecialchars($project['description']) ?>
                    </p>

                    <div class="project-funding">
                        <strong>$<?= number_format($project['current_amount'], 2) ?></strong>
                        <span>of $<?= number_format($project['goal_amount'], 2) ?> goal</span>
                    </div>

                    <progress value="<?= min($percentage, 100) ?>" max="100"></progress>

                    <div class="project-info">
                        <span><?= round($percentage) ?>% funded</span>
                        <span>Ends <?= htmlspecialchars($project['end_date']) ?></span>
                    </div>

                    <div class="project-actions">
                        <a href="project.php?id=<?= $project['id'] ?>">View</a>
                        <a href="project-edit.php?id=<?= $project['id'] ?>">Edit</a>

                        <form action="project-delete.php" method="post">
                            <input type="hidden" name="id" value="<?= $project['id'] ?>">
                            <button type="submit">Delete</button>
                        </form>
                    </div>
                </article>

            <?php endforeach; ?>

        </section>

    <?php else: ?>

        <div class="empty-state">
            <h2>No projects yet</h2>
            <p>Be the first creator to launch a project.</p>
            <a class="button button-dark" href="project-create.php">Start a project</a>
        </div>

    <?php endif; ?>

</main>

<?php require_once 'includes/footer.php'; ?>
