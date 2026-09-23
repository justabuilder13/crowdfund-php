<?php

require_once 'includes/bootstrap.php';

$search = trim($_GET['search'] ?? '');
$categoryId = isset($_GET['category']) ? (int) $_GET['category'] : 0;

$projectModel = new Project();
$categoryModel = new Category();
$projects = $projectModel->getAllProjects($search, $categoryId);
$categories = $categoryModel->getAllCategories();
$pageTitle = 'Discover';

require_once 'includes/header.php';
?>

<main class="container page-space">
    <section class="projects-header">
        <div>
            <p class="eyebrow">Discover</p>
            <h1>Explore projects</h1>
            <p>Find ideas worth supporting across art, design, technology and more.</p>
        </div>
        <a class="button button-dark" href="project-create.php">Start a project</a>
    </section>

    <form class="filter-bar" method="get">
        <input type="search" name="search" value="<?= e($search) ?>" placeholder="Search projects">
        <select name="category">
            <option value="0">All categories</option>
            <?php foreach ($categories as $category): ?>
                <option value="<?= $category['id'] ?>" <?= $categoryId === (int) $category['id'] ? 'selected' : '' ?>>
                    <?= e($category['name']) ?>
                </option>
            <?php endforeach; ?>
        </select>
        <button class="button button-dark" type="submit">Search</button>
    </form>

    <?php if ($projects): ?>
        <section class="projects-grid">
            <?php foreach ($projects as $project): ?>
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
                        <h2><a href="project.php?id=<?= $project['id'] ?>"><?= e($project['title']) ?></a></h2>
                        <p class="project-description"><?= e($project['description']) ?></p>
                        <progress value="<?= $percentage ?>" max="100"></progress>
                        <div class="project-card-stats">
                            <strong>$<?= number_format((float) $project['current_amount'], 0) ?></strong>
                            <span><?= round($percentage) ?>% funded</span>
                        </div>
                        <div class="project-card-meta">
                            <span>By <?= e($project['user_name']) ?></span>
                            <span><?= daysLeft($project['end_date']) ?> days left</span>
                        </div>
                    </div>
                </article>
            <?php endforeach; ?>
        </section>
    <?php else: ?>
        <div class="empty-state">
            <h2>No projects found</h2>
            <p>Try another search or category.</p>
        </div>
    <?php endif; ?>
</main>

<?php require_once 'includes/footer.php'; ?>
