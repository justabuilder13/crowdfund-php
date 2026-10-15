<?php

// Configuration de l'application.
// À modifier quand on déplace le projet (Laragon -> WebDev).

// BASE est le préfixe de l'URL quand le projet est dans un sous-dossier.
// Laragon: http://localhost/crowdfunding  -> BASE = '/crowdfunding'
// Serveur intégré de PHP (php -S localhost:8000 router.php) -> BASE = ''
define('BASE', '/crowdfunding');

// ASSET est l'URL du dossier public (css, images).
define('ASSET', BASE . '/public');

// Connexion à la base de données.
define('DB_HOST', 'localhost');
define('DB_NAME', 'crowdfunding');
define('DB_USER', 'root');
define('DB_PASS', '');
