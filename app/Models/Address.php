<?php
declare(strict_types=1);

namespace Atlas\Models;

final class Address
{
    public int $id;
    public int $userId;
    public string $recipientName;
    public string $phone;
    public string $addressLine;
    public ?string $ward = null;
    public ?string $district = null;
    public string $city;
    public bool $isDefault;

    public static function fromRow(array $row): static
    {
        $addr = new static();
        $addr->id            = (int) $row['id'];
        $addr->userId        = (int) $row['user_id'];
        $addr->recipientName = (string) $row['recipient_name'];
        $addr->phone         = (string) $row['phone'];
        $addr->addressLine   = (string) $row['address_line'];
        $addr->ward          = isset($row['ward']) && $row['ward'] !== '' ? (string) $row['ward'] : null;
        $addr->district      = isset($row['district']) && $row['district'] !== '' ? (string) $row['district'] : null;
        $addr->city          = (string) $row['city'];
        $addr->isDefault     = (bool) ($row['is_default'] ?? false);
        return $addr;
    }

    public function fullText(): string
    {
        $parts = array_filter([
            $this->addressLine,
            $this->ward,
            $this->district,
            $this->city,
        ], static fn(?string $part): bool => $part !== null && trim($part) !== '');

        return implode(', ', $parts);
    }
}
