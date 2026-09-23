<?php

require_once 'Classe/Project.php';
require_once 'Classe/User.php';
require_once 'Classe/Category.php';

$projectModel = new Project();
$userModel = new User();
$categoryModel = new Category();

$users = $userModel->getAllUsers();
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
            'user_id' => (int) $_POST['user_id'],
            'category_id' => (int) $_POST['category_id'],
            'title' => trim($_POST['title']),
            'description' => trim($_POST['description']),
            'goal_amount' => (float) $_POST['goal_amount'],
            'current_amount' => 0,
            'start_date' => $startDate,
            'end_date' => $endDate,
            'status' => 'active'
        ];

        $projectId = $projectModel->createProject($data);

        header("Location: project.php?id=$projectId");
        exit;
    }
}

require_once 'includes/header.php';
?>

<main class="container form-page">
    <div class="form-heading">
                <h1>Start a project</h1>
        <p>Enter the main information for your crowdfunding campaign.</p>
    </div>

    <?php if ($error !== ''): ?>
        <p class="message error-message"><?= htmlspecialchars($error) ?></p>
    <?php endif; ?>

    <form class="main-form" method="post">
        <label for="user_id">Creator</label>
        <select name="user_id" id="user_id" required>
            <?php foreach ($users as $user): ?>
                <option value="<?= $user['id'] ?>">
                    <?= htmlspecialchars($user['name']) ?>
                </option>
            <?php endforeach; ?>
        </select>

        <label for="category_id">Category</label>
        <select name="category_id" id="category_id" required>
            <?php foreach ($categories as $category): ?>
                <option value="<?= $category['id'] ?>">
                    <?= htmlspecialchars($category['name']) ?>
                </option>
            <?php endforeach; ?>
        </select>

        <label for="title">Project title</label>
        <input type="text" id="title" name="title" maxlength="150" required>

        <label for="description">Description</label>
        <textarea id="description" name="description" required></textarea>

        <label for="goal_amount">Funding goal</label>
        <input type="number" id="goal_amount" name="goal_amount" min="1" step="0.01" required>

        <div class="form-row">
            <div>
                <label for="start_date">Start date</label>
                <input type="date" id="start_date" name="start_date" required>
            </div>

            <div>
                <label for="end_date">End date</label>
                <input type="date" id="end_date" name="end_date" required>
            </div>
        </div>

        <button class="button button-dark" type="submit">Launch project</button>
    </form>
</main>

<?php require_once 'includes/footer.php'; ?>
