<?php

namespace Omnipay\YooKassa\Tests;


use Omnipay\Tests\GatewayTestCase;
use Omnipay\YooKassa\Gateway;
use Omnipay\YooKassa\Message\CancelRequest;
use Omnipay\YooKassa\Message\CaptureRequest;
use Omnipay\YooKassa\Message\DetailsRequest;
use Omnipay\YooKassa\Message\PurchaseRequest;
use Omnipay\YooKassa\Message\ReceiptDetailsRequest;
use Omnipay\YooKassa\Message\ReceiptsRequest;
use Omnipay\YooKassa\Message\RefundRequest;
use YooKassa\Model\Locale;


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
            ->setSecret($this->secretKey)
            ->setLocale(Locale::RUSSIAN);
    }


    public function testGateway()
    {
        $this->assertSame($this->shopId, $this->gateway->getShopId());
        $this->assertSame($this->secretKey, $this->gateway->getSecret());
        $this->assertSame(Locale::RUSSIAN, $this->gateway->getLocale());
    }


    public function testSupportsPurchase()
    {
        $supportsPurchase = $this->gateway->supportsPurchase();
        $this->assertTrue($supportsPurchase);

        if ($supportsPurchase) {
            $this->assertInstanceOf(PurchaseRequest::class, $this->gateway->purchase());
        }
    }


    public function testPurchaseDefaultParameters()
    {
        $request = $this->gateway->purchase();
        $this->assertTrue($request->getCapture());
    }


    public function testSupportsCancel()
    {
        $supports = $this->gateway->supportsCancel();
        $this->assertTrue($supports);

        if ($supports) {
            $this->assertInstanceOf(CancelRequest::class, $this->gateway->cancel());
        }
    }


    public function testSupportsCapture()
    {
        $supports = $this->gateway->supportsCapture();
        $this->assertTrue($supports);

        if ($supports) {
            $this->assertInstanceOf(CaptureRequest::class, $this->gateway->capture());
        }
    }


    public function testSupportsDetails()
    {
        $supports = $this->gateway->supportsFetchTransaction();
        $this->assertTrue($supports);

        if ($supports) {
            $this->assertInstanceOf(DetailsRequest::class, $this->gateway->fetchTransaction());
        }
    }


    public function testSupportsReceiptDetails()
    {
        $supports = $this->gateway->supportsFetchReceipt();
        $this->assertTrue($supports);

        if ($supports) {
            $this->assertInstanceOf(ReceiptDetailsRequest::class, $this->gateway->fetchReceipt());
        }
    }


    public function testSupportsRefund()
    {
        $supports = $this->gateway->supportsRefund();
        $this->assertTrue($supports);

        if ($supports) {
            $this->assertInstanceOf(RefundRequest::class, $this->gateway->refund());
        }
    }


    public function testSupportsReceipts()
    {
        $supports = $this->gateway->supportsFetchReceipts();
        $this->assertTrue($supports);

        if ($supports) {
            $this->assertInstanceOf(ReceiptsRequest::class, $this->gateway->fetchReceipts());
        }
    }
}