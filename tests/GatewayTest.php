<?php

namespace Omnipay\YooKassa\Tests;


use Omnipay\Tests\GatewayTestCase;
use Omnipay\YooKassa\Gateway;
use Omnipay\YooKassa\Message\PurchaseRequest;


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


    public function testSupportsPurchase()
    {
        $supportsPurchase = $this->gateway->supportsPurchase();
        $this->assertTrue($supportsPurchase);

        if ($supportsPurchase) {
            $this->assertInstanceOf(PurchaseRequest::class, $this->gateway->purchase());
        }
    }
}