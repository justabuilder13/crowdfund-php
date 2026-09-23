<?php

require_once 'includes/bootstrap.php';

$user = requireLogin();
$projectModel = new Project();
$categoryModel = new Category();

$id = (int) ($_GET['id'] ?? 0);
$project = $projectModel->getProject($id);

if (!$project) {
    flash('error', 'Project not found.');
    redirect('projects.php');
}

if ((int) $project['user_id'] !== (int) $user['id']) {
    flash('error', 'You can only edit your own projects.');
    redirect("project.php?id=$id");
}

$categories = $categoryModel->getAllCategories();
$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title = trim($_POST['title'] ?? '');
    $description = trim($_POST['description'] ?? '');
    $categoryId = (int) ($_POST['category_id'] ?? 0);
    $goalAmount = (float) ($_POST['goal_amount'] ?? 0);
    $startDate = $_POST['start_date'] ?? '';
    $endDate = $_POST['end_date'] ?? '';
    $status = $_POST['status'] ?? 'active';

    if ($title === '' || $description === '') {
        $error = 'Title and description are required.';
    } elseif ($categoryId <= 0 || $goalAmount <= 0) {
        $error = 'Category and funding goal are required.';
    } elseif (!$startDate || !$endDate || $endDate <= $startDate) {
        $error = 'The end date must be after the start date.';
    } elseif (!in_array($status, ['active', 'closed'], true)) {
        $error = 'Invalid project status.';
    } else {
        try {
            $image = $project['image'];

            if (!empty($_POST['remove_image'])) {
                deleteUploadedImage($image);
                $image = null;
            }

            $newImage = uploadImage($_FILES['image'] ?? [], 'projects');

            if ($newImage) {
                deleteUploadedImage($image);
                $image = $newImage;
            }

            $projectModel->updateProject($id, [
                'category_id' => $categoryId,
                'title' => $title,
                'description' => $description,
                'image' => $image,
                'goal_amount' => $goalAmount,
                'start_date' => $startDate,
                'end_date' => $endDate,
                'status' => $status
            ]);

            flash('success', 'Project updated successfully.');
            redirect("project.php?id=$id");
        } catch (RuntimeException $exception) {
            $error = $exception->getMessage();
        }
    }

    $project = array_merge($project, [
        'title' => $title,
        'description' => $description,
        'category_id' => $categoryId,
        'goal_amount' => $goalAmount,
        'start_date' => $startDate,
        'end_date' => $endDate,
        'status' => $status
    ]);
}

$pageTitle = 'Edit project';
require_once 'includes/header.php';
?>

<main class="container form-page">
    <div class="form-heading">
        <p class="eyebrow">Manage</p>
        <h1>Edit project</h1>
        <p>Update the public information for your campaign.</p>
    </div>

    <?php if ($error): ?>
        <p class="form-error"><?= e($error) ?></p>
    <?php endif; ?>

    <form class="main-form" method="post" enctype="multipart/form-data">
        <label for="title">Project title</label>
        <input type="text" id="title" name="title" maxlength="150" value="<?= e($project['title']) ?>" required>

        <label for="category_id">Category</label>
        <select id="category_id" name="category_id" required>
            <?php foreach ($categories as $category): ?>
                <option value="<?= $category['id'] ?>" <?= (int) $project['category_id'] === (int) $category['id'] ? 'selected' : '' ?>>
                    <?= e($category['name']) ?>
                </option>
            <?php endforeach; ?>
        </select>

        <label for="description">Description</label>
        <textarea id="description" name="description" rows="8" required><?= e($project['description']) ?></textarea>

        <label for="image">Replace project image</label>
        <?php if (!empty($project['image'])): ?>
            <img class="form-image-preview" src="<?= e($project['image']) ?>" alt="<?= e($project['title']) ?>">
            <label class="check-row"><input type="checkbox" name="remove_image" value="1"> Remove current image</label>
        <?php endif; ?>
        <input type="file" id="image" name="image" accept="image/jpeg,image/png,image/webp">

        <div class="form-grid-2">
            <div>
                <label for="goal_amount">Funding goal</label>
                <input type="number" id="goal_amount" name="goal_amount" min="1" step="0.01" value="<?= e((string) $project['goal_amount']) ?>" required>
            </div>
            <div>
                <label for="status">Status</label>
                <select id="status" name="status">
                    <option value="active" <?= $project['status'] === 'active' ? 'selected' : '' ?>>Active</option>
                    <option value="closed" <?= $project['status'] === 'closed' ? 'selected' : '' ?>>Closed</option>
                </select>
            </div>
        </div>

        <div class="form-grid-2">
            <div>
                <label for="start_date">Start date</label>
                <input type="date" id="start_date" name="start_date" value="<?= e($project['start_date']) ?>" required>
            </div>
            <div>
                <label for="end_date">End date</label>
                <input type="date" id="end_date" name="end_date" value="<?= e($project['end_date']) ?>" required>
            </div>
        </div>

        <button class="button button-dark" type="submit">Save changes</button>
    </form>
</main>

<?php require_once 'includes/footer.php'; ?>
