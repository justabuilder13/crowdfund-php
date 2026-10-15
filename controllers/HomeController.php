<?php

namespace App\Controllers;

use App\Providers\View;

class HomeController
{
    // Page d'accueil: vue statique (aucune donnée de la BD).
    public function index(): void
    {
        View::render('home');
    }
}
