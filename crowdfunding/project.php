<?php

require_once 'includes/bootstrap.php';

$id = (int) ($_GET['id'] ?? 0);
$projectModel = new Project();
$rewardModel = new Reward();
$pledgeModel = new Pledge();
$commentModel = new Comment();

$project = $projectModel->getProject($id);

if (!$project) {
    flash('error', 'Project not found.');
    redirect('projects.php');
}

$rewards = $rewardModel->getRewardsByProject($id);
$comments = $commentModel->getCommentsByProject($id);
$backerCount = $pledgeModel->countBackers($id);
$percentage = projectPercentage($project);
$user = currentUser();
$isOwner = $user && (int) $user['id'] === (int) $project['user_id'];
$pageTitle = $project['title'];

require_once 'includes/header.php';
?>

<main>
    <section class="campaign-heading container">
        <p class="project-category"><?= e($project['category_name']) ?></p>
        <h1><?= e($project['title']) ?></h1>
        <p class="campaign-byline">By <a href="profile.php?id=<?= $project['user_id'] ?>"><?= e($project['user_name']) ?></a></p>
    </section>

    <section class="campaign-top container">
        <div class="campaign-media">
            <?php if (!empty($project['image'])): ?>
                <img src="<?= e($project['image']) ?>" alt="<?= e($project['title']) ?>">
            <?php else: ?>
                <div class="campaign-placeholder">Project image</div>
            <?php endif; ?>
        </div>

        <aside class="campaign-summary">
            <div class="summary-progress"></div>
            <strong class="campaign-money">$<?= number_format((float) $project['current_amount'], 2) ?></strong>
            <p>pledged of $<?= number_format((float) $project['goal_amount'], 2) ?> goal</p>

            <div class="campaign-numbers">
                <div><strong><?= $backerCount ?></strong><span>backers</span></div>
                <div><strong><?= daysLeft($project['end_date']) ?></strong><span>days to go</span></div>
                <div><strong><?= round($percentage) ?>%</strong><span>funded</span></div>
            </div>

            <?php if ($project['status'] === 'active'): ?>
                <a class="button button-green button-wide" href="pledge-create.php?project_id=<?= $project['id'] ?>">Back this project</a>
            <?php else: ?>
                <span class="button button-disabled button-wide">Campaign closed</span>
            <?php endif; ?>

            <p class="campaign-deadline">Campaign ends <?= e($project['end_date']) ?></p>
        </aside>
    </section>

    <div class="campaign-nav">
        <div class="container campaign-nav-inner">
            <a href="#about">Campaign</a>
            <a href="#rewards">Rewards</a>
            <a href="#comments">Comments</a>
            <?php if ($isOwner): ?>
                <div class="campaign-owner-links">
                    <a href="project-edit.php?id=<?= $project['id'] ?>">Edit project</a>
                    <a href="reward-create.php?project_id=<?= $project['id'] ?>">Add reward</a>
                </div>
            <?php endif; ?>
        </div>
    </div>

    <section class="campaign-content container">
        <article class="campaign-story" id="about">
            <h2>About this project</h2>
            <div class="story-copy"><?= nl2br(e($project['description'])) ?></div>

            <div class="creator-box">
                <p class="eyebrow">Creator</p>
                <h3><?= e($project['user_name']) ?></h3>
                <p><?= e($project['user_email']) ?></p>
                <a href="profile.php?id=<?= $project['user_id'] ?>">View creator profile</a>
            </div>

            <section class="comments-section" id="comments">
                <div class="section-heading compact-heading">
                    <div>
                        <p class="eyebrow">Community</p>
                        <h2>Comments</h2>
                    </div>
                </div>

                <?php if ($comments): ?>
                    <div class="comment-list">
                        <?php foreach ($comments as $comment): ?>
                            <article class="comment-card">
                                <div class="comment-topline">
                                    <strong><?= e($comment['user_name']) ?></strong>
                                    <span><?= e(date('M j, Y', strtotime($comment['created_date']))) ?></span>
                                </div>
                                <p><?= nl2br(e($comment['comment'])) ?></p>
                                <?php if ($user && ((int) $user['id'] === (int) $comment['user_id'] || $isOwner)): ?>
                                    <form action="comment-delete.php" method="post">
                                        <input type="hidden" name="id" value="<?= $comment['id'] ?>">
                                        <button class="text-danger" type="submit">Delete</button>
                                    </form>
                                <?php endif; ?>
                            </article>
                        <?php endforeach; ?>
                    </div>
                <?php else: ?>
                    <p class="muted">No comments yet.</p>
                <?php endif; ?>

                <?php if ($user): ?>
                    <form class="main-form comment-form" action="comment-create.php" method="post">
                        <input type="hidden" name="project_id" value="<?= $project['id'] ?>">
                        <label for="comment">Add a comment</label>
                        <textarea id="comment" name="comment" rows="4" required></textarea>
                        <button class="button button-dark" type="submit">Post comment</button>
                    </form>
                <?php else: ?>
                    <p><a href="login.php">Log in</a> to join the discussion.</p>
                <?php endif; ?>
            </section>
        </article>

        <aside class="reward-column" id="rewards">
            <h2>Rewards</h2>

            <?php if ($rewards): ?>
                <?php foreach ($rewards as $reward): ?>
                    <article class="reward-card">
                        <?php if (!empty($reward['image'])): ?>
                            <img src="<?= e($reward['image']) ?>" alt="<?= e($reward['title']) ?>">
                        <?php endif; ?>
                        <p class="reward-amount">Pledge $<?= number_format((float) $reward['minimum_amount'], 2) ?> or more</p>
                        <h3><?= e($reward['title']) ?></h3>
                        <p><?= nl2br(e($reward['description'])) ?></p>

                        <?php if (!empty($reward['estimated_delivery'])): ?>
                            <div class="reward-meta"><span>Estimated delivery</span><strong><?= e(date('M Y', strtotime($reward['estimated_delivery']))) ?></strong></div>
                        <?php endif; ?>

                        <div class="reward-meta">
                            <span>Availability</span>
                            <strong><?= $reward['quantity'] === null ? 'Unlimited' : (int) $reward['quantity'] . ' left' ?></strong>
                        </div>

                        <?php if ($project['status'] === 'active' && ($reward['quantity'] === null || (int) $reward['quantity'] > 0)): ?>
                            <a class="button button-green button-wide" href="pledge-create.php?project_id=<?= $project['id'] ?>&reward_id=<?= $reward['id'] ?>">Select reward</a>
                        <?php endif; ?>

                        <?php if ($isOwner): ?>
                            <div class="reward-owner-actions">
                                <a href="reward-edit.php?id=<?= $reward['id'] ?>">Edit</a>
                                <form action="reward-delete.php" method="post">
                                    <input type="hidden" name="id" value="<?= $reward['id'] ?>">
                                    <button class="text-danger" type="submit">Delete</button>
                                </form>
                            </div>
                        <?php endif; ?>
                    </article>
                <?php endforeach; ?>
            <?php else: ?>
                <div class="empty-card">
                    <p>No rewards have been added yet.</p>
                    <?php if ($isOwner): ?>
                        <a href="reward-create.php?project_id=<?= $project['id'] ?>">Add the first reward</a>
                    <?php endif; ?>
                </div>
            <?php endif; ?>
        </aside>
    </section>

    <?php if ($isOwner): ?>
        <section class="container danger-zone">
            <div>
                <h2>Project management</h2>
                <p>You can update or permanently delete this campaign.</p>
            </div>
            <div class="danger-actions">
                <a class="button button-light" href="project-edit.php?id=<?= $project['id'] ?>">Edit project</a>
                <form action="project-delete.php" method="post" onsubmit="return confirm('Delete this project permanently?');">
                    <input type="hidden" name="id" value="<?= $project['id'] ?>">
                    <button class="button button-danger" type="submit">Delete project</button>
                </form>
            </div>
        </section>
    <?php endif; ?>
</main>

<?php require_once 'includes/footer.php'; ?>
