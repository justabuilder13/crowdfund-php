<?php

function e(?string $value): string
{
    return htmlspecialchars($value ?? '', ENT_QUOTES, 'UTF-8');
}

function redirect(string $url): never
{
    header("Location: $url");
    exit;
}

function flash(string $key, ?string $message = null): ?string
{
    if ($message !== null) {
        $_SESSION['flash'][$key] = $message;
        return null;
    }

    if (!isset($_SESSION['flash'][$key])) {
        return null;
    }

    $value = $_SESSION['flash'][$key];
    unset($_SESSION['flash'][$key]);
    return $value;
}

function currentUser(): ?array
{
    static $loaded = false;
    static $user = null;

    if ($loaded) {
        return $user;
    }

    $loaded = true;

    if (empty($_SESSION['user_id'])) {
        return null;
    }

    $model = new User();
    $result = $model->getUser((int) $_SESSION['user_id']);
    $user = $result ?: null;
    return $user;
}

function requireLogin(): array
{
    $user = currentUser();

    if (!$user) {
        flash('error', 'Please log in to continue.');
        redirect('login.php');
    }

    return $user;
}

function uploadImage(array $file, string $folder): ?string
{
    if (($file['error'] ?? UPLOAD_ERR_NO_FILE) === UPLOAD_ERR_NO_FILE) {
        return null;
    }

    if (($file['error'] ?? UPLOAD_ERR_OK) !== UPLOAD_ERR_OK) {
        throw new RuntimeException('The image could not be uploaded.');
    }

    if (($file['size'] ?? 0) > 5 * 1024 * 1024) {
        throw new RuntimeException('The image must be 5 MB or smaller.');
    }

    $mime = (new finfo(FILEINFO_MIME_TYPE))->file($file['tmp_name']);
    $allowed = [
        'image/jpeg' => 'jpg',
        'image/png' => 'png',
        'image/webp' => 'webp'
    ];

    if (!isset($allowed[$mime])) {
        throw new RuntimeException('Only JPG, PNG and WebP images are accepted.');
    }

    $directory = __DIR__ . '/../assets/img/' . $folder;

    if (!is_dir($directory) && !mkdir($directory, 0755, true) && !is_dir($directory)) {
        throw new RuntimeException('The image folder could not be created.');
    }

    $filename = bin2hex(random_bytes(12)) . '.' . $allowed[$mime];
    $target = $directory . '/' . $filename;

    if (!move_uploaded_file($file['tmp_name'], $target)) {
        throw new RuntimeException('The image could not be saved.');
    }

    return 'assets/img/' . $folder . '/' . $filename;
}

function deleteUploadedImage(?string $path): void
{
    if (!$path || !str_starts_with($path, 'assets/img/')) {
        return;
    }

    $fullPath = __DIR__ . '/../' . $path;

    if (is_file($fullPath)) {
        unlink($fullPath);
    }
}

function projectPercentage(array $project): float
{
    $goal = (float) $project['goal_amount'];

    if ($goal <= 0) {
        return 0;
    }

    return min(100, ((float) $project['current_amount'] / $goal) * 100);
}

function daysLeft(string $endDate): int
{
    $today = new DateTime('today');
    $end = new DateTime($endDate);

    if ($end < $today) {
        return 0;
    }

    return (int) $today->diff($end)->format('%a');
}
