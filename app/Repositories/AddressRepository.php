<?php
declare(strict_types=1);

namespace Atlas\Repositories;

use Atlas\Core\Database;
use Atlas\Models\Address;
use PDO;

final class AddressRepository
{
    private PDO $db;

    public function __construct()
    {
        $this->db = Database::connection();
    }

    /** @return Address[] */
    public function listByUser(int $userId): array
    {
        $stmt = $this->db->prepare(
            'SELECT * FROM addresses WHERE user_id = :user_id ORDER BY is_default DESC, id DESC'
        );
        $stmt->execute(['user_id' => $userId]);

        return array_map(
            static fn(array $row): Address => Address::fromRow($row),
            $stmt->fetchAll()
        );
    }

    public function findForUser(int $addressId, int $userId): ?Address
    {
        $stmt = $this->db->prepare(
            'SELECT * FROM addresses WHERE id = :id AND user_id = :user_id LIMIT 1'
        );
        $stmt->execute(['id' => $addressId, 'user_id' => $userId]);
        $row = $stmt->fetch();

        return $row ? Address::fromRow($row) : null;
    }

    public function getDefault(int $userId): ?Address
    {
        $stmt = $this->db->prepare(
            'SELECT * FROM addresses WHERE user_id = :user_id ORDER BY is_default DESC, id ASC LIMIT 1'
        );
        $stmt->execute(['user_id' => $userId]);
        $row = $stmt->fetch();

        return $row ? Address::fromRow($row) : null;
    }

    public function create(int $userId, array $data): int
    {
        return Database::transaction(function (PDO $pdo) use ($userId, $data): int {
            $countStmt = $pdo->prepare('SELECT COUNT(*) FROM addresses WHERE user_id = :user_id');
            $countStmt->execute(['user_id' => $userId]);
            $hasExisting = ((int) $countStmt->fetchColumn()) > 0;

            // Địa chỉ đầu tiên tự động là mặc định
            $isDefault = !$hasExisting || !empty($data['is_default']);

            if ($isDefault && $hasExisting) {
                $resetStmt = $pdo->prepare('UPDATE addresses SET is_default = 0 WHERE user_id = :user_id');
                $resetStmt->execute(['user_id' => $userId]);
            }

            $stmt = $pdo->prepare(
                'INSERT INTO addresses (user_id, recipient_name, phone, address_line, ward, district, city, is_default)
                 VALUES (:user_id, :recipient_name, :phone, :address_line, :ward, :district, :city, :is_default)'
            );
            $stmt->execute([
                'user_id'        => $userId,
                'recipient_name' => trim((string) $data['recipient_name']),
                'phone'          => trim((string) $data['phone']),
                'address_line'   => trim((string) $data['address_line']),
                'ward'           => !empty($data['ward']) ? trim((string) $data['ward']) : null,
                'district'       => !empty($data['district']) ? trim((string) $data['district']) : null,
                'city'           => trim((string) $data['city']),
                'is_default'     => $isDefault ? 1 : 0,
            ]);

            return (int) $pdo->lastInsertId();
        });
    }

    public function update(int $addressId, int $userId, array $data): void
    {
        $stmt = $this->db->prepare(
            'UPDATE addresses
             SET recipient_name = :recipient_name,
                 phone = :phone,
                 address_line = :address_line,
                 ward = :ward,
                 district = :district,
                 city = :city
             WHERE id = :id AND user_id = :user_id'
        );
        $stmt->execute([
            'id'             => $addressId,
            'user_id'        => $userId,
            'recipient_name' => trim((string) $data['recipient_name']),
            'phone'          => trim((string) $data['phone']),
            'address_line'   => trim((string) $data['address_line']),
            'ward'           => !empty($data['ward']) ? trim((string) $data['ward']) : null,
            'district'       => !empty($data['district']) ? trim((string) $data['district']) : null,
            'city'           => trim((string) $data['city']),
        ]);
    }

    public function delete(int $addressId, int $userId): void
    {
        $stmt = $this->db->prepare('DELETE FROM addresses WHERE id = :id AND user_id = :user_id');
        $stmt->execute(['id' => $addressId, 'user_id' => $userId]);
    }

    public function setDefault(int $addressId, int $userId): void
    {
        Database::transaction(function (PDO $pdo) use ($addressId, $userId): void {
            $check = $pdo->prepare('SELECT id FROM addresses WHERE id = :id AND user_id = :user_id LIMIT 1');
            $check->execute(['id' => $addressId, 'user_id' => $userId]);
            if (!$check->fetch()) {
                return;
            }

            $unset = $pdo->prepare('UPDATE addresses SET is_default = 0 WHERE user_id = :user_id');
            $unset->execute(['user_id' => $userId]);

            $set = $pdo->prepare('UPDATE addresses SET is_default = 1 WHERE id = :id AND user_id = :user_id');
            $set->execute(['id' => $addressId, 'user_id' => $userId]);
        });
    }
}
