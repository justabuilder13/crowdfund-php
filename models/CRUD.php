<?php

namespace App\Models;

// Classe parent de tous les modèles.
// Ouvre la connexion PDO et contient les méthodes CRUD réutilisables.
// Chaque modèle enfant définit seulement $table et $fillable.
abstract class CRUD extends \PDO
{
    protected string $table = '';
    protected string $primaryKey = 'id';
    protected array $fillable = [];

    final public function __construct()
    {
        parent::__construct(
            'mysql:host=' . DB_HOST . ';dbname=' . DB_NAME . ';charset=utf8mb4',
            DB_USER,
            DB_PASS
        );

        $this->setAttribute(\PDO::ATTR_ERRMODE, \PDO::ERRMODE_EXCEPTION);
        $this->setAttribute(\PDO::ATTR_DEFAULT_FETCH_MODE, \PDO::FETCH_ASSOC);
    }

    // READ - Toutes les lignes de la table.
    final public function select(?string $field = null, string $order = 'ASC'): array
    {
        $field = $field ?? $this->primaryKey;
        $order = strtoupper($order) === 'DESC' ? 'DESC' : 'ASC';

        $sql = "SELECT * FROM $this->table ORDER BY $field $order";

        return $this->query($sql)->fetchAll();
    }

    // READ - Une ligne selon son id. Retourne false si elle n'existe pas.
    final public function selectId(int|string $value): array|false
    {
        $sql = "SELECT * FROM $this->table WHERE $this->primaryKey = :value";
        $stmt = $this->prepare($sql);
        $stmt->bindValue(':value', $value);
        $stmt->execute();

        return $stmt->fetch();
    }

    // CREATE - Ajoute une ligne et retourne le nouvel id.
    final public function insert(array $data): int|false
    {
        $data = $this->onlyFillable($data);

        $fieldNames = implode(', ', array_keys($data));
        $fieldValues = ':' . implode(', :', array_keys($data));

        $sql = "INSERT INTO $this->table ($fieldNames) VALUES ($fieldValues)";
        $stmt = $this->prepare($sql);

        foreach ($data as $key => $value) {
            $stmt->bindValue(":$key", $value);
        }

        return $stmt->execute() ? (int) $this->lastInsertId() : false;
    }

    // UPDATE - Modifie une ligne selon son id.
    final public function update(array $data, int|string $id): bool
    {
        $data = $this->onlyFillable($data);

        $fields = [];
        foreach (array_keys($data) as $key) {
            $fields[] = "$key = :$key";
        }
        $fieldNames = implode(', ', $fields);

        $sql = "UPDATE $this->table SET $fieldNames WHERE $this->primaryKey = :primaryKey";
        $stmt = $this->prepare($sql);

        foreach ($data as $key => $value) {
            $stmt->bindValue(":$key", $value);
        }
        $stmt->bindValue(':primaryKey', $id);

        return $stmt->execute();
    }

    // DELETE - Supprime une ligne selon son id.
    final public function delete(int|string $id): bool
    {
        $sql = "DELETE FROM $this->table WHERE $this->primaryKey = :id";
        $stmt = $this->prepare($sql);
        $stmt->bindValue(':id', $id);

        return $stmt->execute();
    }

    // Garde seulement les colonnes permises ($fillable).
    // Protège la table contre des champs ajoutés à un formulaire.
    private function onlyFillable(array $data): array
    {
        return array_intersect_key($data, array_flip($this->fillable));
    }
}
