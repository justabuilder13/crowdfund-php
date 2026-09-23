<?php

require_once 'includes/bootstrap.php';

$projectModel = new Project();
$featuredProjects = $projectModel->getFeaturedProjects(3);
$pageTitle = 'Home';

require_once 'includes/header.php';
?>

<main>
    <section class="hero container">
        <p class="eyebrow">Crowdfunding for creative ideas</p>
        <h1>Bring something new into the world.</h1>
        <p class="hero-copy">Discover independent projects, support creators and help ambitious ideas move forward.</p>
        <div class="hero-actions">
            <a class="button button-dark" href="projects.php">Explore projects</a>
            <a class="button button-light" href="project-create.php">Start a project</a>
        </div>
    </section>

    <section class="container section-block">
        <div class="section-heading">
            <div>
                <p class="eyebrow">Featured</p>
                <h2>Projects gaining momentum</h2>
            </div>
            <a href="projects.php">View all projects</a>
        </div>

        <?php if ($featuredProjects): ?>
            <div class="projects-grid">
                <?php foreach ($featuredProjects as $project): ?>
                    <?php $percentage = projectPercentage($project); ?>
                    <article class="project-card">
                        <a class="project-image-wrap" href="project.php?id=<?= $project['id'] ?>">
                            <?php if (!empty($project['image'])): ?>
                                <img class="project-image" src="<?= e($project['image']) ?>" alt="<?= e($project['title']) ?>">
                            <?php else: ?>
                                <div class="project-image project-placeholder">No image yet</div>
                            <?php endif; ?>
                        </a>
                        <div class="project-card-body">
                            <p class="project-category"><?= e($project['category_name']) ?></p>
                            <h3><a href="project.php?id=<?= $project['id'] ?>"><?= e($project['title']) ?></a></h3>
                            <p class="project-description"><?= e($project['description']) ?></p>
                            <progress value="<?= $percentage ?>" max="100"></progress>
                            <div class="project-card-stats">
                                <strong>$<?= number_format((float) $project['current_amount'], 0) ?></strong>
                                <span><?= round($percentage) ?>% funded</span>
                            </div>
                            <p class="project-creator">By <?= e($project['user_name']) ?></p>
                        </div>
                    </article>
                <?php endforeach; ?>
            </div>
        <?php else: ?>
            <div class="empty-state">
                <h3>No active projects yet</h3>
                <p>Launch the first campaign on Crowdfund.</p>
                <a class="button button-dark" href="project-create.php">Start a project</a>
            </div>
        <?php endif; ?>
    </section>
</main>

<?php require_once 'includes/footer.php'; ?>
