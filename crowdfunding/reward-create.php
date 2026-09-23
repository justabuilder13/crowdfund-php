<?php

require_once 'includes/bootstrap.php';

$user = requireLogin();
$projectId = (int) ($_GET['project_id'] ?? $_POST['project_id'] ?? 0);
$projectModel = new Project();
$rewardModel = new Reward();
$project = $projectModel->getProject($projectId);

if (!$project || (int) $project['user_id'] !== (int) $user['id']) {
    flash('error', 'You cannot add rewards to this project.');
    redirect('projects.php');
}

$error = '';
$title = '';
$description = '';
$minimumAmount = '';
$quantity = '';
$estimatedDelivery = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title = trim($_POST['title'] ?? '');
    $description = trim($_POST['description'] ?? '');
    $minimumAmount = trim($_POST['minimum_amount'] ?? '');
    $quantity = trim($_POST['quantity'] ?? '');
    $estimatedDelivery = $_POST['estimated_delivery'] ?? '';

    if ($title === '' || !is_numeric($minimumAmount) || (float) $minimumAmount <= 0) {
        $error = 'Reward title and a valid minimum pledge are required.';
    } elseif ($quantity !== '' && (!ctype_digit($quantity) || (int) $quantity < 1)) {
        $error = 'Quantity must be empty or greater than 0.';
    } else {
        try {
            $image = uploadImage($_FILES['image'] ?? [], 'rewards');

            $rewardModel->createReward([
                'project_id' => $projectId,
                'title' => $title,
                'description' => $description,
                'image' => $image,
                'minimum_amount' => (float) $minimumAmount,
                'quantity' => $quantity === '' ? null : (int) $quantity,
                'estimated_delivery' => $estimatedDelivery ?: null
            ]);

            flash('success', 'Reward added.');
            redirect("project.php?id=$projectId#rewards");
        } catch (RuntimeException $exception) {
            $error = $exception->getMessage();
        }
    }
}

$pageTitle = 'Add reward';
require_once 'includes/header.php';
?>

<main class="container form-page">
    <div class="form-heading">
        <p class="eyebrow">Reward</p>
        <h1>Add a reward</h1>
        <p><?= e($project['title']) ?></p>
    </div>

    <?php if ($error): ?>
        <p class="form-error"><?= e($error) ?></p>
    <?php endif; ?>

    <form class="main-form" method="post" enctype="multipart/form-data">
        <input type="hidden" name="project_id" value="<?= $projectId ?>">

        <label for="title">Reward title</label>
        <input type="text" id="title" name="title" maxlength="150" value="<?= e($title) ?>" required>

        <label for="description">Description</label>
        <textarea id="description" name="description" rows="5"><?= e($description) ?></textarea>

        <label for="image">Reward image</label>
        <input type="file" id="image" name="image" accept="image/jpeg,image/png,image/webp">

        <div class="form-grid-2">
            <div>
                <label for="minimum_amount">Minimum pledge</label>
                <input type="number" id="minimum_amount" name="minimum_amount" min="1" step="0.01" value="<?= e($minimumAmount) ?>" required>
            </div>
            <div>
                <label for="quantity">Quantity</label>
                <input type="number" id="quantity" name="quantity" min="1" value="<?= e($quantity) ?>" placeholder="Unlimited">
            </div>
        </div>

        <label for="estimated_delivery">Estimated delivery</label>
        <input type="date" id="estimated_delivery" name="estimated_delivery" value="<?= e($estimatedDelivery) ?>">

        <button class="button button-dark" type="submit">Add reward</button>
    </form>
</main>

<?php require_once 'includes/footer.php'; ?>
