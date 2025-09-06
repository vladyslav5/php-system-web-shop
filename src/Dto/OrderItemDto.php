<?php

namespace App\Dto;

use Symfony\Component\Validator\Constraints as Assert;

class OrderItemDto
{


    #[Assert\NotBlank]
    private string $productName;

    #[Assert\NotBlank]
    #[Assert\GreaterThan(0)]
    private int $quantity;

    #[Assert\NotBlank]
    #[Assert\GreaterThan(0)]
    private float $price;

    public function __construct(int $quantity = 1,
                                float|int $price = 1.0,
                                string $productName = '')
    {
        $this->quantity = $quantity;
        $this->price = (float)$price;
        $this->productName = $productName;
    }

    public function getQuantity(): int
    {
        return $this->quantity;
    }
    public function setQuantity(int $quantity): void
    {
        $this->quantity = $quantity;
    }


    public function getPrice(): float
    {
        return $this->price;
    }

    public function getProductName(): string
    {
        return $this->productName;
    }
    public function setPrice(float $price): void
    {
        $this->price = $price;
    }
    public function setProductName(string $productName): void
    {
        $this->productName = $productName;
    }
}
