<?php

require_once 'includes/bootstrap.php';

if (currentUser()) {
    redirect('index.php');
}

$error = '';
$email = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';

    $userModel = new User();
    $user = $userModel->authenticate($email, $password);

    if ($user) {
        $_SESSION['user_id'] = (int) $user['id'];
        flash('success', 'Welcome back, ' . $user['name'] . '.');
        redirect('index.php');
    }

    $error = 'Email or password is incorrect.';
}

$pageTitle = 'Log in';
require_once 'includes/header.php';
?>

<main class="container auth-page">
    <section class="auth-card">
        <p class="eyebrow">Welcome back</p>
        <h1>Log in</h1>

        <?php if ($error): ?>
            <p class="form-error"><?= e($error) ?></p>
        <?php endif; ?>

        <form class="main-form" method="post">
            <label for="email">Email</label>
            <input type="email" id="email" name="email" value="<?= e($email) ?>" required>

            <label for="password">Password</label>
            <input type="password" id="password" name="password" required>

            <button class="button button-dark" type="submit">Log in</button>
        </form>

        <p class="auth-switch">No account yet? <a href="register.php">Create one</a>.</p>
    </section>
</main>

<?php require_once 'includes/footer.php'; ?>
