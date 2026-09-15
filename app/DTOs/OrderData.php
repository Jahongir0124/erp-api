<?php




namespace app\DTOs;



final class OrderData
{
    public function __construct(
        public readonly int $customerId,
        public readonly array $items
        )
    {}

    public static function fromArray(array $data): self
    {
        return new self(
            customerId: $data['customer_id'],
            items: $data['items']
        );
    }
}