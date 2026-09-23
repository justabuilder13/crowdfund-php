<?php

// Classe générale pour la connexion PDO et les opérations CRUD réutilisables.
class CRUD extends PDO
{
    public function __construct()
    {
        // Connexion à la base de données crowdfunding.
        parent::__construct(
            'mysql:host=localhost;dbname=crowdfunding;port=3306;charset=utf8mb4',
            'root',
            ''
        );

        // Affiche clairement les erreurs PDO pendant le développement.
        $this->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

        // Les résultats PDO sont retournés sous forme de tableaux associatifs.
        $this->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
    }

    // READ - Afficher toutes les données d'une table.
    public function select(string $table, string $field = 'id', string $order = 'ASC'): array
    {
        $order = strtoupper($order);

        // On accepte seulement ASC ou DESC pour éviter une valeur invalide.
        if ($order !== 'ASC' && $order !== 'DESC') {
            $order = 'ASC';
        }

        $sql = "SELECT * FROM $table ORDER BY $field $order";
        $stmt = $this->query($sql);

        return $stmt->fetchAll();
    }

    // READ - Afficher une donnée selon son ID ou un autre champ.
    public function selectId(string $table, int|string $value, string $field = 'id')
    {
        $sql = "SELECT * FROM $table WHERE $field = :value";
        $stmt = $this->prepare($sql);
        $stmt->bindValue(':value', $value);
        $stmt->execute();

        return $stmt->fetch();
    }

    // CREATE - Ajouter une donnée et retourner l'ID créé.
    public function insert(string $table, array $data): int
    {
        $fieldName = implode(', ', array_keys($data));
        $fieldBindValue = ':' . implode(', :', array_keys($data));

        $sql = "INSERT INTO $table ($fieldName) VALUES ($fieldBindValue)";
        $stmt = $this->prepare($sql);

        foreach ($data as $key => $value) {
            $stmt->bindValue(":$key", $value);
        }

        $stmt->execute();

        return (int) $this->lastInsertId();
    }

    // UPDATE - Modifier une donnée existante.
    public function update(
        string $table,
        array $data,
        int|string $value,
        string $field = 'id'
    ): bool {
        $fieldName = '';

        foreach ($data as $key => $dataValue) {
            $fieldName .= "$key = :$key, ";
        }

        $fieldName = rtrim($fieldName, ', ');

        $sql = "UPDATE $table SET $fieldName WHERE $field = :whereValue";
        $stmt = $this->prepare($sql);

        foreach ($data as $key => $dataValue) {
            $stmt->bindValue(":$key", $dataValue);
        }

        $stmt->bindValue(':whereValue', $value);

        return $stmt->execute();
    }

    // DELETE - Supprimer une donnée.
    public function delete(
        string $table,
        int|string $value,
        string $field = 'id'
    ): bool {
        $sql = "DELETE FROM $table WHERE $field = :value";
        $stmt = $this->prepare($sql);
        $stmt->bindValue(':value', $value);

        return $stmt->execute();
    }
}
