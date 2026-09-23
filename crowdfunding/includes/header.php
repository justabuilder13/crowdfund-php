<?php
$user = currentUser();
$successMessage = flash('success');
$errorMessage = flash('error');
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= isset($pageTitle) ? e($pageTitle) . ' · ' : '' ?>Crowdfund</title>
    <link rel="stylesheet" href="css/style.css">
</head>
<body>
<header class="site-header">
    <nav class="site-nav">
        <a class="logo" href="index.php">Crowdfund</a>
        <a href="projects.php">Discover</a>
        <a href="project-create.php">Start a project</a>
        <div class="nav-spacer"></div>
        <?php if ($user): ?>
            <a href="profile.php?id=<?= $user['id'] ?>"><?= e($user['name']) ?></a>
            <a href="logout.php">Log out</a>
        <?php else: ?>
            <a href="login.php">Log in</a>
            <a class="nav-signup" href="register.php">Sign up</a>
        <?php endif; ?>
    </nav>
</header>

<?php if ($successMessage): ?>
    <div class="flash flash-success"><?= e($successMessage) ?></div>
<?php endif; ?>

<?php if ($errorMessage): ?>
    <div class="flash flash-error"><?= e($errorMessage) ?></div>
<?php endif; ?>
