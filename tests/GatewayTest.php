<?php

namespace Omnipay\YooKassa\Tests;


use Omnipay\Tests\GatewayTestCase;
use Omnipay\YooKassa\Gateway;


class GatewayTest extends GatewayTestCase
{
    public $gateway;

    private $shopId = '1';
    private $secretKey = 'test_1';


    protected function setUp(): void
    {
        parent::setUp();

        $this->gateway = new Gateway($this->getHttpClient(), $this->getHttpRequest());
        $this->gateway->setTestMode(true)
            ->setShopId($this->shopId)
            ->setSecret($this->secretKey);
    }


    public function testGateway()
    {
        $this->assertSame($this->shopId, $this->gateway->getShopId());
        $this->assertSame($this->secretKey, $this->gateway->getSecret());
    }
}