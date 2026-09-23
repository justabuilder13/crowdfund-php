<?php

require_once 'includes/bootstrap.php';

$user = requireLogin();
$categoryModel = new Category();
$projectModel = new Project();
$categories = $categoryModel->getAllCategories();

$error = '';
$title = '';
$description = '';
$goalAmount = '';
$categoryId = 0;
$startDate = date('Y-m-d');
$endDate = date('Y-m-d', strtotime('+30 days'));

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title = trim($_POST['title'] ?? '');
    $description = trim($_POST['description'] ?? '');
    $goalAmount = trim($_POST['goal_amount'] ?? '');
    $categoryId = (int) ($_POST['category_id'] ?? 0);
    $startDate = $_POST['start_date'] ?? '';
    $endDate = $_POST['end_date'] ?? '';

    if ($title === '' || $description === '') {
        $error = 'Title and description are required.';
    } elseif ($categoryId <= 0) {
        $error = 'Please select a category.';
    } elseif (!is_numeric($goalAmount) || (float) $goalAmount <= 0) {
        $error = 'The funding goal must be greater than $0.';
    } elseif (!$startDate || !$endDate || $endDate <= $startDate) {
        $error = 'The end date must be after the start date.';
    } else {
        try {
            $image = uploadImage($_FILES['image'] ?? [], 'projects');

            $projectId = $projectModel->createProject([
                'user_id' => (int) $user['id'],
                'category_id' => $categoryId,
                'title' => $title,
                'description' => $description,
                'image' => $image,
                'goal_amount' => (float) $goalAmount,
                'current_amount' => 0,
                'start_date' => $startDate,
                'end_date' => $endDate,
                'status' => 'active'
            ]);

            flash('success', 'Your project has been created.');
            redirect("project.php?id=$projectId");
        } catch (RuntimeException $exception) {
            $error = $exception->getMessage();
        }
    }
}

$pageTitle = 'Start a project';
require_once 'includes/header.php';
?>

<main class="container form-page">
    <div class="form-heading">
        <p class="eyebrow">Create</p>
        <h1>Start a project</h1>
        <p>Tell people what you want to make, set a goal and launch your campaign.</p>
    </div>

    <?php if ($error): ?>
        <p class="form-error"><?= e($error) ?></p>
    <?php endif; ?>

    <form class="main-form" method="post" enctype="multipart/form-data">
        <label for="title">Project title</label>
        <input type="text" id="title" name="title" maxlength="150" value="<?= e($title) ?>" required>

        <label for="category_id">Category</label>
        <select id="category_id" name="category_id" required>
            <option value="">Choose a category</option>
            <?php foreach ($categories as $category): ?>
                <option value="<?= $category['id'] ?>" <?= $categoryId === (int) $category['id'] ? 'selected' : '' ?>>
                    <?= e($category['name']) ?>
                </option>
            <?php endforeach; ?>
        </select>

        <label for="description">Description</label>
        <textarea id="description" name="description" rows="8" required><?= e($description) ?></textarea>

        <label for="image">Project image</label>
        <input type="file" id="image" name="image" accept="image/jpeg,image/png,image/webp">
        <small>Optional. JPG, PNG or WebP, maximum 5 MB.</small>

        <div class="form-grid-2">
            <div>
                <label for="goal_amount">Funding goal</label>
                <input type="number" id="goal_amount" name="goal_amount" min="1" step="0.01" value="<?= e($goalAmount) ?>" required>
            </div>
            <div>
                <label>Creator</label>
                <input type="text" value="<?= e($user['name']) ?>" disabled>
            </div>
        </div>

        <div class="form-grid-2">
            <div>
                <label for="start_date">Start date</label>
                <input type="date" id="start_date" name="start_date" value="<?= e($startDate) ?>" required>
            </div>
            <div>
                <label for="end_date">End date</label>
                <input type="date" id="end_date" name="end_date" value="<?= e($endDate) ?>" required>
            </div>
        </div>

        <button class="button button-dark" type="submit">Launch project</button>
    </form>
</main>

<?php require_once 'includes/footer.php'; ?>
