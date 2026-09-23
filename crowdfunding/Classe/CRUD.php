<?php

class CRUD extends PDO
{
    public function __construct()
    {
        parent::__construct(
            'mysql:host=localhost;dbname=crowdfunding;port=3306;charset=utf8mb4',
            'root',
            ''
        );

        $this->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        $this->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
    }

    public function select(string $table, string $field = 'id', string $order = 'ASC'): array
    {
        $order = strtoupper($order) === 'DESC' ? 'DESC' : 'ASC';
        $sql = "SELECT * FROM $table ORDER BY $field $order";
        return $this->query($sql)->fetchAll();
    }

    public function selectId(string $table, int|string $value, string $field = 'id')
    {
        $sql = "SELECT * FROM $table WHERE $field = :value";
        $stmt = $this->prepare($sql);
        $stmt->bindValue(':value', $value);
        $stmt->execute();
        return $stmt->fetch();
    }

    public function insert(string $table, array $data): int
    {
        $fields = implode(', ', array_keys($data));
        $binds = ':' . implode(', :', array_keys($data));
        $sql = "INSERT INTO $table ($fields) VALUES ($binds)";
        $stmt = $this->prepare($sql);

        foreach ($data as $key => $value) {
            $stmt->bindValue(":$key", $value);
        }

        $stmt->execute();
        return (int) $this->lastInsertId();
    }

    public function update(string $table, array $data, int|string $value, string $field = 'id'): bool
    {
        $parts = [];

        foreach ($data as $key => $item) {
            $parts[] = "$key = :$key";
        }

        $sql = "UPDATE $table SET " . implode(', ', $parts) . " WHERE $field = :whereValue";
        $stmt = $this->prepare($sql);

        foreach ($data as $key => $item) {
            $stmt->bindValue(":$key", $item);
        }

        $stmt->bindValue(':whereValue', $value);
        return $stmt->execute();
    }

    public function delete(string $table, int|string $value, string $field = 'id'): bool
    {
        $sql = "DELETE FROM $table WHERE $field = :value";
        $stmt = $this->prepare($sql);
        $stmt->bindValue(':value', $value);
        return $stmt->execute();
    }
}
