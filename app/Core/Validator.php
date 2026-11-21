<?php
declare(strict_types=1);

namespace Atlas\Core;

final class Validator
{
    private array $data;
    private array $errors = [];
    private array $validatedFields = [];

    public function __construct(array $data)
    {
        $this->data = $data;
    }

    private function value(string $field): mixed
    {
        $this->validatedFields[$field] = true;
        $val = $this->data[$field] ?? null;
        return is_string($val) ? trim($val) : $val;
    }

    private function addError(string $field, string $message): void
    {
        if (!isset($this->errors[$field])) {
            $this->errors[$field] = $message;
        }
    }

    private function isEmpty(mixed $val): bool
    {
        return $val === null || $val === '';
    }

    public function required(string $field, string $label): self
    {
        $val = $this->value($field);
        if ($this->isEmpty($val)) {
            $this->addError($field, "{$label} is required.");
        }
        return $this;
    }

    public function email(string $field, string $label): self
    {
        $val = $this->value($field);
        if (!$this->isEmpty($val) && filter_var($val, FILTER_VALIDATE_EMAIL) === false) {
            $this->addError($field, "{$label} is not a valid email address.");
        }
        return $this;
    }

    public function minLength(string $field, int $n, string $label): self
    {
        $val = $this->value($field);
        if (!$this->isEmpty($val) && mb_strlen((string) $val, 'UTF-8') < $n) {
            $this->addError($field, "{$label} must be at least {$n} characters.");
        }
        return $this;
    }

    public function maxLength(string $field, int $n, string $label): self
    {
        $val = $this->value($field);
        if (!$this->isEmpty($val) && mb_strlen((string) $val, 'UTF-8') > $n) {
            $this->addError($field, "{$label} must not exceed {$n} characters.");
        }
        return $this;
    }

    public function integer(string $field, string $label, ?int $min = null, ?int $max = null): self
    {
        $val = $this->value($field);
        if ($this->isEmpty($val)) {
            return $this;
        }

        if (filter_var($val, FILTER_VALIDATE_INT) === false) {
            $this->addError($field, "{$label} must be an integer.");
            return $this;
        }

        $intVal = (int) $val;
        if ($min !== null && $intVal < $min) {
            $this->addError($field, "{$label} must be at least {$min}.");
        }
        if ($max !== null && $intVal > $max) {
            $this->addError($field, "{$label} must not exceed {$max}.");
        }
        return $this;
    }

    public function in(string $field, array $allowed, string $label): self
    {
        $val = $this->value($field);
        if (!$this->isEmpty($val) && !in_array($val, $allowed, true)) {
            $this->addError($field, "{$label} has an invalid selection.");
        }
        return $this;
    }

    public function same(string $field, string $otherField, string $label): self
    {
        $val = $this->value($field);
        $otherVal = $this->data[$otherField] ?? null;
        if (is_string($otherVal)) {
            $otherVal = trim($otherVal);
        }

        if ($val !== $otherVal) {
            $this->addError($field, "{$label} does not match.");
        }
        return $this;
    }

    public function phoneVn(string $field, string $label): self
    {
        $val = $this->value($field);
        if (!$this->isEmpty($val) && preg_match('/^(0|\+84)\d{9}$/', (string) $val) !== 1) {
            $this->addError($field, "{$label} must be a valid Vietnamese phone number.");
        }
        return $this;
    }

    public function passes(): bool
    {
        return empty($this->errors);
    }

    public function errors(): array
    {
        return $this->errors;
    }

    public function validated(): array
    {
        $result = [];
        foreach (array_keys($this->validatedFields) as $field) {
            $val = $this->data[$field] ?? null;
            $result[$field] = is_string($val) ? trim($val) : $val;
        }
        return $result;
    }
}
