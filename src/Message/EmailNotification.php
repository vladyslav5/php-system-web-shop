<?php

namespace App\Message;
readonly class EmailNotification
{
    public function __construct(
        private int           $orderId,
        private EmailTypeEnum $type
    )
    {

    }

    public function getOrderId(): int
    {
        return $this->orderId;
    }

    public function getType(): EmailTypeEnum
    {
        return $this->type;
    }


}

