<?php

declare(strict_types=1);


namespace Tests\Api;

use App\Entity\Order;
use Tests\Support\ApiTester;

final class ViewOrderCest
{
    public function _before(ApiTester $I): void
    {
    }

    public function tryToTest(ApiTester $I): void
    {
        $order = $I->grabEntityFromRepository(Order::class, []);
        $I->sendGET('/api/orders/' . $order->getId());
        $I->seeResponseCodeIs(\Codeception\Util\HttpCode::OK);
        $I->seeResponseIsJson();

        $I->seeResponseContainsJson([
            'id' => $order->getId(),
            'customerEmail' => $order->getCustomerEmail(),
            'customerName' => $order->getCustomerName(),
            'totalAmount' => $order->getTotalAmount(),
        ]);

    }
}
