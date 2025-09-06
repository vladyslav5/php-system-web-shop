<?php

namespace App\Dto;

use App\Entity\Order;
use App\Entity\OrderItem;

readonly class ViewOrderDto
{
    public function __construct(
        public int    $id,
        public string $customerName,
        public string $customerEmail,
        public float  $totalAmount,
        public string $createdAt,
        public string $updatedAt,
        public string $status,
        /** @var OrderItem[] $orderItems */
        public array  $orderItems,
    )
    {
    }

    public static function fromEntity(Order $order): self
    {
        return new self(
            id: $order->getId(),
            customerName: $order->getCustomerName(),
            customerEmail: $order->getCustomerEmail(),
            totalAmount: $order->getTotalAmount(),
            createdAt:  $order->getCreatedAt()->format('Y-m-d H:i:s'),
            updatedAt: $order->getUpdatedAt()->format('Y-m-d H:i:s'),
            status: $order->getStatus()->getName(),
            orderItems: $order->getOrderItems()->toArray(),
        );
    }
}
