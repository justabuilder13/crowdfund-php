<?php

require_once 'Classe/Project.php';

$projectModel = new Project();
$projects = $projectModel->getAllProjects();

$featuredProject = $projects[0] ?? null;
$recommendedProjects = array_slice($projects, 1, 4);
$moreProjects = array_slice($projects, 0, 6);

function projectImage(array $project): ?string
{
    foreach (['jpg', 'jpeg', 'png', 'webp'] as $extension) {
        $path = "assets/img/project-{$project['id']}.$extension";

        if (file_exists(__DIR__ . '/' . $path)) {
            return $path;
        }
    }

    return null;
}

function projectPercentage(array $project): float
{
    if ((float) $project['goal_amount'] <= 0) {
        return 0;
    }

    return ((float) $project['current_amount'] / (float) $project['goal_amount']) * 100;
}

require_once 'includes/header.php';
?>

<main>
    <section class="home-top">
        <div>
            <h1>Discover projects worth backing.</h1>
            <p>Explore ideas from independent creators and help bring them to life.</p>
        </div>
        <a href="projects.php">Browse all projects</a>
    </section>

    <?php if ($featuredProject): ?>
        <?php
        $featuredImage = projectImage($featuredProject);
        $featuredPercentage = projectPercentage($featuredProject);
        ?>

        <section class="discovery-layout">
            <div class="featured-block">
                <div class="section-title-row">
                    <h2>Featured project</h2>
                </div>

                <a class="featured-project" href="project.php?id=<?= $featuredProject['id'] ?>">
                    <div class="featured-image">
                        <?php if ($featuredImage): ?>
                            <img src="<?= htmlspecialchars($featuredImage) ?>" alt="<?= htmlspecialchars($featuredProject['title']) ?>">
                        <?php else: ?>
                            <div class="image-placeholder"></div>
                        <?php endif; ?>
                    </div>

                    <div class="featured-content">
                        <h3><?= htmlspecialchars($featuredProject['title']) ?></h3>
                        <p class="featured-description"><?= htmlspecialchars($featuredProject['description']) ?></p>
                        <p class="project-byline">By <?= htmlspecialchars($featuredProject['user_name']) ?></p>

                        <div class="funding-bar">
                            <span style="width: <?= min($featuredPercentage, 100) ?>%"></span>
                        </div>

                        <div class="funding-meta">
                            <strong><?= round($featuredPercentage) ?>% funded</strong>
                            <span><?= htmlspecialchars($featuredProject['category_name']) ?></span>
                        </div>
                    </div>
                </a>
            </div>

            <aside class="recommended-block">
                <div class="section-title-row">
                    <h2>Recommended</h2>
                    <a href="projects.php">View all</a>
                </div>

                <div class="recommendation-list">
                    <?php foreach ($recommendedProjects as $project): ?>
                        <?php
                        $image = projectImage($project);
                        $percentage = projectPercentage($project);
                        ?>

                        <a class="recommendation-item" href="project.php?id=<?= $project['id'] ?>">
                            <div class="recommendation-image">
                                <?php if ($image): ?>
                                    <img src="<?= htmlspecialchars($image) ?>" alt="<?= htmlspecialchars($project['title']) ?>">
                                <?php else: ?>
                                    <div class="image-placeholder"></div>
                                <?php endif; ?>
                            </div>

                            <div>
                                <h3><?= htmlspecialchars($project['title']) ?></h3>
                                <p><?= round($percentage) ?>% funded</p>
                                <span>By <?= htmlspecialchars($project['user_name']) ?></span>
                            </div>
                        </a>
                    <?php endforeach; ?>
                </div>
            </aside>
        </section>

        <section class="more-projects">
            <div class="section-title-row">
                <h2>More projects</h2>
                <a href="projects.php">See everything</a>
            </div>

            <div class="home-project-grid">
                <?php foreach ($moreProjects as $project): ?>
                    <?php
                    $image = projectImage($project);
                    $percentage = projectPercentage($project);
                    ?>

                    <a class="home-project-card" href="project.php?id=<?= $project['id'] ?>">
                        <div class="home-project-image">
                            <?php if ($image): ?>
                                <img src="<?= htmlspecialchars($image) ?>" alt="<?= htmlspecialchars($project['title']) ?>">
                            <?php else: ?>
                                <div class="image-placeholder"></div>
                            <?php endif; ?>
                        </div>

                        <div class="home-project-copy">
                            <h3><?= htmlspecialchars($project['title']) ?></h3>
                            <p><?= htmlspecialchars($project['description']) ?></p>
                            <div>
                                <strong><?= round($percentage) ?>% funded</strong>
                                <span><?= htmlspecialchars($project['category_name']) ?></span>
                            </div>
                        </div>
                    </a>
                <?php endforeach; ?>
            </div>
        </section>
    <?php else: ?>
        <section class="home-empty">
            <h2>No projects yet.</h2>
            <p>Create the first project on Crowdfund.</p>
            <a class="button button-dark" href="project-create.php">Start a project</a>
        </section>
    <?php endif; ?>
</main>

<?php require_once 'includes/footer.php'; ?>
