<?php

namespace App\Providers;

use Twig\Environment;
use Twig\Loader\FilesystemLoader;

// Affiche les templates Twig et gère les redirections et les messages flash.
class View
{
    // Affiche un template du dossier views.
    public static function render(string $template, array $data = []): void
    {
        $loader = new FilesystemLoader(dirname(__DIR__) . '/views');
        $twig = new Environment($loader);

        // Variables disponibles dans tous les templates.
        $twig->addGlobal('asset', ASSET);
        $twig->addGlobal('base', BASE);
        $twig->addGlobal('flash', self::getFlash());

        echo $twig->render($template . '.php', $data);
    }

    // Redirige vers une autre page, avec un message de confirmation ou d'erreur optionnel.
    public static function redirect(string $url, ?string $message = null, string $type = 'success'): void
    {
        if ($message !== null) {
            $_SESSION['flash'] = ['message' => $message, 'type' => $type];
        }

        header('Location: ' . BASE . '/' . ltrim($url, '/'));
        exit;
    }

    // Lit le message flash une seule fois, puis l'enlève de la session.
    private static function getFlash(): ?array
    {
        $flash = $_SESSION['flash'] ?? null;
        unset($_SESSION['flash']);

        return $flash;
    }
}
