<?php

namespace Omnipay\YooKassa\Tests\Message;

use DateTime;
use Omnipay\Common\Exception\InvalidRequestException;
use Omnipay\YooKassa\Message\ReceiptDetailsRequest;
use Omnipay\YooKassa\Message\ReceiptResponse;
use ReflectionException;


class ReceiptDetailsRequestTest extends AbstractRequestTest
{
    protected string $requestName = ReceiptDetailsRequest::class;

    private string $id = 'rt-2da5c87d-0384-50e8-a7f3-8d5646dd9e10';


    public function testGetData()
    {
        $this->request->initialize(['receiptId' => $this->id]);

        $data = $this->request->getData();

        $this->assertNull($data);
        $this->assertSame($this->id, $this->request->getId());
    }


    public function testBadGetData()
    {
        $this->expectException(InvalidRequestException::class);

        $this->request->initialize([]);

        $this->request->getData();
    }

    /**
     * @throws ReflectionException
     */
    public function testSendData()
    {
        $this->request->initialize(['receiptId' => $this->id]);

        $response = $this->sendMockRequest($this->request, 'receipt.succeeded');
        $this->assertInstanceOf(ReceiptResponse::class, $response);

        if ($response instanceof ReceiptResponse) {
            $this->assertTrue($response->isSuccessful());
            $this->assertSame($this->id, $response->getReceiptId());
            $this->assertTrue($response->isPayment());
            $this->assertTrue($response->isSucceeded());
            $this->assertSame("225d8da0-000f-50be-b000-0003308c89be", $response->getPaymentId());
            $this->assertSame("3997", $response->getFiscalDocumentNumber());
            $this->assertSame("9288000100115786", $response->getFiscalStorageNumber());
            $this->assertSame("2617603922", $response->getFiscalAttribute());
            $this->assertSame("fd9e9404-eaca-4000-8ec9-dc228ead2346", $response->getFiscalProviderId());
            $this->assertEquals(new DateTime("2019-09-18T10:06:42.985Z"), $response->getRegisteredDate());
            $this->assertEquals(55.68, $response->getAmount());

            $items = $response->getItems();
            $this->assertSame("Сapybara", $items[0]->getDescription());
            $this->assertSame("commodity", $items[0]->getPaymentSubject());
            $this->assertSame("full_payment", $items[0]->getPaymentMode());
            $this->assertSame(2, $items[0]->getVatCode());
            $this->assertEquals(5, $items[0]->getQuantity());
            $this->assertEquals(7501.55, $items[0]->getPrice());

            $this->assertSame("item 2", $items[1]->getDescription());
            $this->assertSame("commodity", $items[1]->getPaymentSubject());
            $this->assertSame("full_payment", $items[1]->getPaymentMode());
            $this->assertSame(2, $items[1]->getVatCode());
            $this->assertEquals(2.005, $items[1]->getQuantity());
            $this->assertEquals(2.41, $items[1]->getPrice());
        }
    }
}