<?php

declare(strict_types=1);


namespace Tests\Api;

use Tests\Support\ApiTester;

final class CreateOrderCest
{
    public function _before(ApiTester $I): void
    {
        // Code here will be executed before each test.
    }

    public function tryToTest(ApiTester $I): void
    {
        $I->wantTo('create a new order via API');
        $orderData = [
            'customerName' => 'John Doe',
            'customerEmail' => 'email1@example.com',
            'orderItems' => [
                [
                    'productName' => 'milo',
                    'quantity' => 2,
                    'price' => 2000
                ],
                [
                    'productName' => 'telephon',
                    'quantity' => 1,
                    'price' => 1999.99
                ]
            ]
        ];
        $I->haveHttpHeader('Content-Type', 'application/json');
        $I->sendPOST('/api/orders', json_encode($orderData));
        $I->seeResponseCodeIsSuccessful();
        $I->seeResponseCodeIs(201);
        $I->seeResponseIsJson();
        $I->seeResponseContainsJson(['status' => [
            'id'=>1,
            'name'=>"pending"
        ]]);
    }
}
