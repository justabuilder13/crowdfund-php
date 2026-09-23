<?php

require_once 'includes/bootstrap.php';

$user = requireLogin();
$rewardModel = new Reward();
$projectModel = new Project();
$id = (int) ($_GET['id'] ?? 0);
$reward = $rewardModel->getReward($id);

if (!$reward) {
    flash('error', 'Reward not found.');
    redirect('projects.php');
}

$project = $projectModel->getProject((int) $reward['project_id']);

if (!$project || (int) $project['user_id'] !== (int) $user['id']) {
    flash('error', 'You cannot edit this reward.');
    redirect('projects.php');
}

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title = trim($_POST['title'] ?? '');
    $description = trim($_POST['description'] ?? '');
    $minimumAmount = (float) ($_POST['minimum_amount'] ?? 0);
    $quantityRaw = trim($_POST['quantity'] ?? '');
    $estimatedDelivery = $_POST['estimated_delivery'] ?? '';

    if ($title === '' || $minimumAmount <= 0) {
        $error = 'Reward title and a valid minimum pledge are required.';
    } else {
        try {
            $image = $reward['image'];

            if (!empty($_POST['remove_image'])) {
                deleteUploadedImage($image);
                $image = null;
            }

            $newImage = uploadImage($_FILES['image'] ?? [], 'rewards');
            if ($newImage) {
                deleteUploadedImage($image);
                $image = $newImage;
            }

            $rewardModel->updateReward($id, [
                'title' => $title,
                'description' => $description,
                'image' => $image,
                'minimum_amount' => $minimumAmount,
                'quantity' => $quantityRaw === '' ? null : max(0, (int) $quantityRaw),
                'estimated_delivery' => $estimatedDelivery ?: null
            ]);

            flash('success', 'Reward updated.');
            redirect('project.php?id=' . $project['id'] . '#rewards');
        } catch (RuntimeException $exception) {
            $error = $exception->getMessage();
        }
    }

    $reward = array_merge($reward, [
        'title' => $title,
        'description' => $description,
        'minimum_amount' => $minimumAmount,
        'quantity' => $quantityRaw === '' ? null : (int) $quantityRaw,
        'estimated_delivery' => $estimatedDelivery
    ]);
}

$pageTitle = 'Edit reward';
require_once 'includes/header.php';
?>

<main class="container form-page">
    <div class="form-heading">
        <p class="eyebrow">Reward</p>
        <h1>Edit reward</h1>
        <p><?= e($project['title']) ?></p>
    </div>

    <?php if ($error): ?>
        <p class="form-error"><?= e($error) ?></p>
    <?php endif; ?>

    <form class="main-form" method="post" enctype="multipart/form-data">
        <label for="title">Reward title</label>
        <input type="text" id="title" name="title" maxlength="150" value="<?= e($reward['title']) ?>" required>

        <label for="description">Description</label>
        <textarea id="description" name="description" rows="5"><?= e($reward['description']) ?></textarea>

        <?php if (!empty($reward['image'])): ?>
            <img class="form-image-preview" src="<?= e($reward['image']) ?>" alt="<?= e($reward['title']) ?>">
            <label class="check-row"><input type="checkbox" name="remove_image" value="1"> Remove current image</label>
        <?php endif; ?>

        <label for="image">Replace reward image</label>
        <input type="file" id="image" name="image" accept="image/jpeg,image/png,image/webp">

        <div class="form-grid-2">
            <div>
                <label for="minimum_amount">Minimum pledge</label>
                <input type="number" id="minimum_amount" name="minimum_amount" min="1" step="0.01" value="<?= e((string) $reward['minimum_amount']) ?>" required>
            </div>
            <div>
                <label for="quantity">Quantity remaining</label>
                <input type="number" id="quantity" name="quantity" min="0" value="<?= $reward['quantity'] === null ? '' : (int) $reward['quantity'] ?>" placeholder="Unlimited">
            </div>
        </div>

        <label for="estimated_delivery">Estimated delivery</label>
        <input type="date" id="estimated_delivery" name="estimated_delivery" value="<?= e($reward['estimated_delivery']) ?>">

        <button class="button button-dark" type="submit">Save reward</button>
    </form>
</main>

<?php require_once 'includes/footer.php'; ?>
