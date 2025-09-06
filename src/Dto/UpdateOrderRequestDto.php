<?php

namespace App\Dto;

use App\ObjectMapper\IsNotNullCondition;
use Symfony\Component\ObjectMapper\Attribute\Map;
use Symfony\Component\Validator\Constraints as Assert;

class UpdateOrderRequestDto
{
    #[Assert\Email]
    #[Assert\NotBlank(allowNull: true)]
    #[Map(if: IsNotNullCondition::class)]
    private ?string $customerEmail = null;

    #[Assert\NotBlank(allowNull: true)]
    #[Assert\Length(min: 3, max: 255)]
    #[Map(if: IsNotNullCondition::class)]
    private ?string $customerName = null;

    #[Assert\Valid]
    #[Assert\Count(min: 1, minMessage: "Order must have at least one item.")]
    /** @var OrderItemDto[]|null */
    #[Map(target: OrderItemDto::class)]
//    #[Map(if: IsNotNullCondition::class)]
    private ?array $orderItems = null;

    public function __construct(
        ?string $customerEmail = null,
        ?string $customerName = null,
        ?array  $orderItems = null
    )
    {
        $this->customerEmail = $customerEmail;
        $this->customerName = $customerName;
        $this->orderItems = $orderItems;
    }

    public function calculateTotal(): float
    {
        if (empty($this->orderItems)) {
            return 0;
        }

        $sum = 0;
        foreach ($this->orderItems as $item) {
            $sum += $item['price'] * $item['quantity'];
        }

        return $sum;
    }

    public function getCustomerEmail(): ?string
    {
        return $this->customerEmail;
    }

    public function getCustomerName(): ?string
    {
        return $this->customerName;
    }

    /**
     * @return OrderItemDto[]|null
     */
    public function getOrderItems(): ?array
    {
        return $this->orderItems;
    }
}
