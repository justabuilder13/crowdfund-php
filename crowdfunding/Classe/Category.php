<?php

require_once __DIR__ . '/CRUD.php';

class Category extends CRUD
{
    private string $table = 'categories';

    public function getAllCategories(): array
    {
        return $this->select($this->table, 'name');
    }

    public function getCategory(int $id)
    {
        return $this->selectId($this->table, $id);
    }
}
