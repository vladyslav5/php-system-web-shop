<?php

namespace App\Dto;

use App\Entity\OrderItem;
use Symfony\Component\ObjectMapper\Attribute\Map;
use Symfony\Component\Validator\Constraints as Assert;
class CreateOrderRequestDto
{
    #[Assert\NotBlank]
    #[Assert\Email]
    private string $customerEmail;
    #[Assert\NotBlank]
    #[Assert\Length(min: 3, max: 255)]
    private string $customerName;


    #[Assert\Valid]
    #[Assert\Count(min: 1, minMessage: "Order must have at least one item.")]
    /** @var OrderItemDTO[] $orderItems */
    #[Map(target: OrderItemDto::class)]
    private array $orderItems;

    public function __construct(
        string $customerEmail,
        string $customerName,
        array  $orderItems
    )
    {
        $this->customerEmail = $customerEmail;
        $this->customerName = $customerName;
        $this->orderItems = $orderItems;

    }

    public function calculateTotal(): float
    {

        $sum = 0;
        foreach ($this->orderItems as $item) {

            $sum += $item['price'] * $item['quantity'];
        }

        return  $sum;
    }

    public
    function getCustomerEmail(): string
    {
        return $this->customerEmail;
    }

    public
    function getCustomerName(): string
    {
        return $this->customerName;
    }



    /**
     * @return OrderItemDto[]
     */
    public
    function getOrderItems(): array
    {
        return $this->orderItems;
    }

}
