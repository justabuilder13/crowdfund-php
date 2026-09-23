<?php

require_once 'includes/bootstrap.php';

$current = currentUser();
$id = isset($_GET['id']) ? (int) $_GET['id'] : (int) ($current['id'] ?? 0);

if ($id <= 0) {
    flash('error', 'Log in to view your profile.');
    redirect('login.php');
}

$userModel = new User();
(new Project())->refreshExpiredStatuses();
$profile = $userModel->getUser($id);

if (!$profile) {
    flash('error', 'User not found.');
    redirect('index.php');
}

$projects = $userModel->getUserProjects($id);
$pledges = $userModel->getUserPledges($id);
$isOwnProfile = $current && (int) $current['id'] === $id;
$pageTitle = $profile['name'];

require_once 'includes/header.php';
?>

<main class="container page-space">
    <section class="profile-hero">
        <div class="profile-avatar"><?= e(strtoupper(substr($profile['name'], 0, 1))) ?></div>
        <div>
            <p class="eyebrow">Creator profile</p>
            <h1><?= e($profile['name']) ?></h1>
            <p>Member since <?= e(date('F Y', strtotime($profile['created_date']))) ?></p>
        </div>
    </section>

    <section class="section-block">
        <div class="section-heading">
            <div>
                <p class="eyebrow">Created</p>
                <h2>Projects</h2>
            </div>
            <?php if ($isOwnProfile): ?>
                <a class="button button-dark" href="project-create.php">Start a project</a>
            <?php endif; ?>
        </div>

        <?php if ($projects): ?>
            <div class="projects-grid">
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
                            <div class="project-title-row">
                                <p class="project-category"><?= e($project['category_name']) ?></p>
                                <span class="status-pill status-<?= e($project['status']) ?>"><?= e(ucfirst($project['status'])) ?></span>
                            </div>
                            <h3><a href="project.php?id=<?= $project['id'] ?>"><?= e($project['title']) ?></a></h3>
                            <progress value="<?= $percentage ?>" max="100"></progress>
                            <div class="project-card-stats">
                                <strong>$<?= number_format((float) $project['current_amount'], 0) ?></strong>
                                <span><?= round($percentage) ?>% funded</span>
                            </div>
                            <?php if ($isOwnProfile): ?>
                                <div class="card-actions">
                                    <a href="project-edit.php?id=<?= $project['id'] ?>">Edit</a>
                                    <a href="reward-create.php?project_id=<?= $project['id'] ?>">Add reward</a>
                                </div>
                            <?php endif; ?>
                        </div>
                    </article>
                <?php endforeach; ?>
            </div>
        <?php else: ?>
            <div class="empty-state"><p>No projects yet.</p></div>
        <?php endif; ?>
    </section>

    <?php if ($isOwnProfile): ?>
        <section class="section-block">
            <div class="section-heading">
                <div>
                    <p class="eyebrow">Supported</p>
                    <h2>Your pledges</h2>
                </div>
            </div>

            <?php if ($pledges): ?>
                <div class="table-wrap">
                    <table>
                        <thead>
                            <tr><th>Project</th><th>Amount</th><th>Status</th><th>Date</th></tr>
                        </thead>
                        <tbody>
                            <?php foreach ($pledges as $pledge): ?>
                                <tr>
                                    <td><a href="project.php?id=<?= $pledge['project_id'] ?>"><?= e($pledge['project_title']) ?></a></td>
                                    <td>$<?= number_format((float) $pledge['amount'], 2) ?></td>
                                    <td><?= e(ucfirst($pledge['payment_status'])) ?></td>
                                    <td><?= e(date('M j, Y', strtotime($pledge['created_date']))) ?></td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            <?php else: ?>
                <div class="empty-state"><p>You have not backed a project yet.</p></div>
            <?php endif; ?>
        </section>
    <?php endif; ?>
</main>

<?php require_once 'includes/footer.php'; ?>
