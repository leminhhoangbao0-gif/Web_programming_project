<?php
namespace Atlas\Models;

final class Payment
{
    public int $id;
    public int $orderId;
    public string $method;
    public int $amount;
    public string $status;
    public ?string $transactionRef;
    public string $createdAt;

    public static function fromRow(array $row): static
    {
        $p = new static();
        $p->id = (int)$row['id'];
        $p->orderId = (int)$row['order_id'];
        $p->method = $row['method'];
        $p->amount = (int)$row['amount'];
        $p->status = $row['status'];
        $p->transactionRef = $row['transaction_ref'];
        $p->createdAt = $row['created_at'];
        return $p;
    }
}
