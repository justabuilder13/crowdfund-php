<?php

require_once __DIR__ . '/CRUD.php';

// Classe représentant l'entité Category.
class Category extends CRUD
{
    private string $table = 'categories';

    // READ - Afficher toutes les catégories en ordre alphabétique.
    public function getAllCategories(): array
    {
        return $this->select($this->table, 'name');
    }

    // READ - Afficher une catégorie selon son ID.
    public function getCategory(int $id)
    {
        return $this->selectId($this->table, $id);
    }
}
