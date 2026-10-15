<?php

// Remplace le .htaccess avec le serveur intégré de PHP.
// Dans le dossier du projet: php -S localhost:8000 router.php
// (mettre BASE à '' dans config.php dans ce cas)

$path = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
$file = __DIR__ . $path;

if ($path !== '/' && is_file($file)) {
    return false;
}

require __DIR__ . '/index.php';
