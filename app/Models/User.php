<?php
declare(strict_types=1);

namespace Atlas\Models;

final class User
{
    public int $id;
    public string $role;
    public string $fullName;
    public string $email;
    public ?string $phone = null;
    public string $status;
    public ?string $lastLoginAt = null;
    public string $createdAt;

    public static function fromRow(array $row): static
    {
        $user = new static();
        $user->id          = (int) $row['id'];
        $user->role        = (string) $row['role'];
        $user->fullName    = (string) $row['full_name'];
        $user->email       = (string) $row['email'];
        $user->phone       = isset($row['phone']) ? (string) $row['phone'] : null;
        $user->status      = (string) ($row['status'] ?? 'active');
        $user->lastLoginAt = isset($row['last_login_at']) ? (string) $row['last_login_at'] : null;
        $user->createdAt   = (string) ($row['created_at'] ?? '');
        return $user;
    }

    public function isAdmin(): bool
    {
        return $this->role === 'admin';
    }

    public function isActive(): bool
    {
        return $this->status === 'active';
    }
}
