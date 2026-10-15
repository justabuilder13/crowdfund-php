<?php

// Point d'entrée unique: toutes les requêtes passent par ce fichier (voir .htaccess).

session_start();

require_once __DIR__ . '/vendor/autoload.php';
require_once __DIR__ . '/config.php';

use App\Providers\View;

// Gestion globale des erreurs: une exception non attrapée affiche une page
// d'erreur propre au lieu du message PHP.
set_exception_handler(function (Throwable $error) {
    error_log($error->getMessage());
    http_response_code(500);
    View::render('error', ['msg' => 'Something went wrong. Please try again later.']);
});

require_once __DIR__ . '/routes/web.php';
