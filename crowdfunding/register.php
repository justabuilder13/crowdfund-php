<?php

require_once 'includes/bootstrap.php';

if (currentUser()) {
    redirect('index.php');
}

$error = '';
$name = '';
$email = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($_POST['name'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';
    $confirmPassword = $_POST['confirm_password'] ?? '';

    if ($name === '' || mb_strlen($name) < 2) {
        $error = 'Please enter your name.';
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = 'Please enter a valid email address.';
    } elseif (strlen($password) < 8) {
        $error = 'The password must contain at least 8 characters.';
    } elseif ($password !== $confirmPassword) {
        $error = 'The passwords do not match.';
    } else {
        $userModel = new User();

        if ($userModel->getUserByEmail($email)) {
            $error = 'An account already exists with this email.';
        } else {
            $userId = $userModel->createUser([
                'name' => $name,
                'email' => $email,
                'password' => $password
            ]);

            $_SESSION['user_id'] = $userId;
            flash('success', 'Your account has been created.');
            redirect('index.php');
        }
    }
}

$pageTitle = 'Sign up';
require_once 'includes/header.php';
?>

<main class="container auth-page">
    <section class="auth-card">
        <p class="eyebrow">Join Crowdfund</p>
        <h1>Create an account</h1>

        <?php if ($error): ?>
            <p class="form-error"><?= e($error) ?></p>
        <?php endif; ?>

        <form class="main-form" method="post">
            <label for="name">Name</label>
            <input type="text" id="name" name="name" maxlength="100" value="<?= e($name) ?>" required>

            <label for="email">Email</label>
            <input type="email" id="email" name="email" value="<?= e($email) ?>" required>

            <label for="password">Password</label>
            <input type="password" id="password" name="password" minlength="8" required>

            <label for="confirm_password">Confirm password</label>
            <input type="password" id="confirm_password" name="confirm_password" minlength="8" required>

            <button class="button button-dark" type="submit">Create account</button>
        </form>

        <p class="auth-switch">Already have an account? <a href="login.php">Log in</a>.</p>
    </section>
</main>

<?php require_once 'includes/footer.php'; ?>
