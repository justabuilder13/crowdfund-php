<?php

namespace App\Providers;

// Valide les données d'un formulaire avant de les enregistrer.
// Exemple: $val->field('title', $data['title'])->required()->max(150);
class Validator
{
    private array $errors = [];
    private string $key = '';
    private string $value = '';
    private string $name = '';

    // Choisit le champ à valider.
    public function field(string $key, mixed $value, ?string $name = null): self
    {
        $this->key = $key;
        $this->value = trim((string) ($value ?? ''));
        $this->name = ucfirst($name ?? str_replace('_', ' ', $key));

        return $this;
    }

    // Règles

    // Le champ ne peut pas être vide.
    public function required(): self
    {
        if ($this->value === '') {
            $this->addError("$this->name is required.");
        }
        return $this;
    }

    // Nombre minimum de caractères.
    public function min(int $length): self
    {
        if ($this->value !== '' && mb_strlen($this->value) < $length) {
            $this->addError("$this->name must be at least $length characters.");
        }
        return $this;
    }

    // Nombre maximum de caractères.
    public function max(int $length): self
    {
        if (mb_strlen($this->value) > $length) {
            $this->addError("$this->name must be $length characters or less.");
        }
        return $this;
    }

    // Doit être un nombre entier.
    public function int(): self
    {
        if ($this->value !== '' && filter_var($this->value, FILTER_VALIDATE_INT) === false) {
            $this->addError("$this->name must be a whole number.");
        }
        return $this;
    }

    // Doit être un nombre (décimales permises).
    public function number(): self
    {
        if ($this->value !== '' && !is_numeric($this->value)) {
            $this->addError("$this->name must be a number.");
        }
        return $this;
    }

    // Le nombre doit être plus grand ou égal à $min.
    public function minValue(float $min): self
    {
        if (is_numeric($this->value) && (float) $this->value < $min) {
            $this->addError("$this->name must be at least " . number_format($min, 2) . '.');
        }
        return $this;
    }

    // Le nombre doit être plus petit ou égal à $max.
    public function maxValue(float $max): self
    {
        if (is_numeric($this->value) && (float) $this->value > $max) {
            $this->addError("$this->name must be " . number_format($max, 2) . ' or less.');
        }
        return $this;
    }

    // Format de courriel valide.
    public function email(): self
    {
        if ($this->value !== '' && !filter_var($this->value, FILTER_VALIDATE_EMAIL)) {
            $this->addError("Invalid $this->name format.");
        }
        return $this;
    }

    // Date valide au format AAAA-MM-JJ.
    public function date(): self
    {
        if ($this->value !== '') {
            $date = \DateTime::createFromFormat('Y-m-d', $this->value);
            if (!$date || $date->format('Y-m-d') !== $this->value) {
                $this->addError("$this->name must be a valid date.");
            }
        }
        return $this;
    }

    // La date doit être après une autre date (les chaînes AAAA-MM-JJ se comparent bien).
    public function after(string $otherDate, string $otherName): self
    {
        if ($this->value !== '' && $otherDate !== '' && $this->value <= $otherDate) {
            $this->addError("$this->name must be after the $otherName.");
        }
        return $this;
    }

    // La valeur doit faire partie des valeurs permises.
    public function in(array $allowed): self
    {
        if ($this->value !== '' && !in_array($this->value, $allowed, true)) {
            $this->addError("$this->name is not valid.");
        }
        return $this;
    }

    // Résultats

    // Garde seulement la première erreur de chaque champ.
    private function addError(string $message): void
    {
        if (!isset($this->errors[$this->key])) {
            $this->errors[$this->key] = $message;
        }
    }

    // Ajoute une erreur personnalisée (ex: règle qui a besoin de la BD).
    public function addCustomError(string $key, string $message): void
    {
        if (!isset($this->errors[$key])) {
            $this->errors[$key] = $message;
        }
    }

    public function isSuccess(): bool
    {
        return empty($this->errors);
    }

    public function getErrors(): array
    {
        return $this->errors;
    }
}
