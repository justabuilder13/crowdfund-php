<?php

require_once 'Classe/Project.php';
require_once 'Classe/Category.php';

$projectModel = new Project();
$categoryModel = new Category();

$id = isset($_GET['id']) ? (int) $_GET['id'] : 0;

if ($id <= 0) {
    header('Location: projects.php');
    exit;
}

$project = $projectModel->getProject($id);

if (!$project) {
    header('Location: projects.php');
    exit;
}

$categories = $categoryModel->getAllCategories();
$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $startDate = $_POST['start_date'] ?? '';
    $endDate = $_POST['end_date'] ?? '';

    // Validation simple : la date de fin doit être après la date de début.
    if ($endDate < $startDate) {
        $error = 'The end date must be after the start date.';
    } else {
        $data = [
            'category_id' => (int) $_POST['category_id'],
            'title' => trim($_POST['title']),
            'description' => trim($_POST['description']),
            'goal_amount' => (float) $_POST['goal_amount'],
            'start_date' => $startDate,
            'end_date' => $endDate,
            'status' => $_POST['status']
        ];

        $projectModel->updateProject($id, $data);

        header("Location: project.php?id=$id");
        exit;
    }
}

require_once 'includes/header.php';
?>

<main class="container form-page">
    <div class="form-heading">
                <h1>Edit project</h1>
        <p>Modify the campaign information and save your changes.</p>
    </div>

    <?php if ($error !== ''): ?>
        <p class="message error-message"><?= htmlspecialchars($error) ?></p>
    <?php endif; ?>

    <form class="main-form" method="post">
        <label for="category_id">Category</label>
        <select name="category_id" id="category_id" required>
            <?php foreach ($categories as $category): ?>
                <option
                    value="<?= $category['id'] ?>"
                    <?= $category['id'] == $project['category_id'] ? 'selected' : '' ?>
                >
                    <?= htmlspecialchars($category['name']) ?>
                </option>
            <?php endforeach; ?>
        </select>

        <label for="title">Project title</label>
        <input
            type="text"
            id="title"
            name="title"
            maxlength="150"
            value="<?= htmlspecialchars($project['title']) ?>"
            required
        >

        <label for="description">Description</label>
        <textarea id="description" name="description" required><?= htmlspecialchars($project['description']) ?></textarea>

        <label for="goal_amount">Funding goal</label>
        <input
            type="number"
            id="goal_amount"
            name="goal_amount"
            min="1"
            step="0.01"
            value="<?= htmlspecialchars($project['goal_amount']) ?>"
            required
        >

        <div class="form-row">
            <div>
                <label for="start_date">Start date</label>
                <input
                    type="date"
                    id="start_date"
                    name="start_date"
                    value="<?= htmlspecialchars($project['start_date']) ?>"
                    required
                >
            </div>

            <div>
                <label for="end_date">End date</label>
                <input
                    type="date"
                    id="end_date"
                    name="end_date"
                    value="<?= htmlspecialchars($project['end_date']) ?>"
                    required
                >
            </div>
        </div>

        <label for="status">Status</label>
        <select name="status" id="status" required>
            <option value="active" <?= $project['status'] === 'active' ? 'selected' : '' ?>>Active</option>
            <option value="inactive" <?= $project['status'] === 'inactive' ? 'selected' : '' ?>>Inactive</option>
        </select>

        <button class="button button-dark" type="submit">Save changes</button>
    </form>
</main>

<?php require_once 'includes/footer.php'; ?>
