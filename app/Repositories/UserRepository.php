<?php
declare(strict_types=1);

namespace Atlas\Repositories;

use Atlas\Core\Database;
use Atlas\Models\User;
use PDO;

final class UserRepository
{
    private PDO $db;

    public function __construct()
    {
        $this->db = Database::connection();
    }

    public function findById(int $id): ?User
    {
        $stmt = $this->db->prepare('SELECT * FROM users WHERE id = :id LIMIT 1');
        $stmt->execute(['id' => $id]);
        $row = $stmt->fetch();

        return $row ? User::fromRow($row) : null;
    }

    public function findByEmail(string $email): ?User
    {
        $stmt = $this->db->prepare('SELECT * FROM users WHERE email = :email LIMIT 1');
        $stmt->execute(['email' => mb_strtolower(trim($email))]);
        $row = $stmt->fetch();

        return $row ? User::fromRow($row) : null;
    }

    public function getPasswordHash(int $userId): string
    {
        $stmt = $this->db->prepare('SELECT password_hash FROM users WHERE id = :id LIMIT 1');
        $stmt->execute(['id' => $userId]);
        $hash = $stmt->fetchColumn();

        return is_string($hash) ? $hash : '';
    }

    public function create(
        string $fullName,
        string $email,
        ?string $phone,
        string $passwordHash,
        string $role = 'customer'
    ): int {
        $stmt = $this->db->prepare(
            'INSERT INTO users (role, full_name, email, phone, password_hash)
             VALUES (:role, :full_name, :email, :phone, :password_hash)'
        );
        $stmt->execute([
            'role'          => $role,
            'full_name'     => trim($fullName),
            'email'         => mb_strtolower(trim($email)),
            'phone'         => ($phone !== null && trim($phone) !== '') ? trim($phone) : null,
            'password_hash' => $passwordHash,
        ]);

        return (int) $this->db->lastInsertId();
    }

    public function updateProfile(int $userId, string $fullName, ?string $phone): void
    {
        $stmt = $this->db->prepare(
            'UPDATE users SET full_name = :full_name, phone = :phone WHERE id = :id'
        );
        $stmt->execute([
            'id'        => $userId,
            'full_name' => trim($fullName),
            'phone'     => ($phone !== null && trim($phone) !== '') ? trim($phone) : null,
        ]);
    }

    public function updatePassword(int $userId, string $passwordHash): void
    {
        $stmt = $this->db->prepare(
            'UPDATE users SET password_hash = :password_hash WHERE id = :id'
        );
        $stmt->execute([
            'id'            => $userId,
            'password_hash' => $passwordHash,
        ]);
    }

    public function touchLastLogin(int $userId): void
    {
        $stmt = $this->db->prepare('UPDATE users SET last_login_at = NOW() WHERE id = :id');
        $stmt->execute(['id' => $userId]);
    }

    public function countCreatedBetween(string $from, string $to): int
    {
        $stmt = $this->db->prepare(
            'SELECT COUNT(*) FROM users WHERE role = :role AND created_at BETWEEN :from AND :to'
        );
        $stmt->execute([
            'role' => 'customer',
            'from' => $from,
            'to'   => $to,
        ]);

        return (int) $stmt->fetchColumn();
    }
}
