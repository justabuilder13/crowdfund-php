<?php

namespace App\Models;

// Modèle de la table categories.
class Category extends CRUD
{
    protected string $table = 'categories';
    protected array $fillable = ['name'];
}
