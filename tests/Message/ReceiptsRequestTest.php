<?php

namespace Omnipay\YooKassa\Tests\Message;

use DateTime;
use Omnipay\YooKassa\Message\ReceiptResponse;
use Omnipay\YooKassa\Message\ReceiptsRequest;
use Omnipay\YooKassa\Message\ReceiptsResponse;
use ReflectionException;


class ReceiptsRequestTest extends AbstractRequestTest
{
    protected string $requestName = ReceiptsRequest::class;

    /**
     * @dataProvider provider
     */
    public function testGetData($provider)
    {
        $this->request->initialize($provider);

        $data = $this->request->getData();

        $this->assertSame("215d8da0-000f-50be-b000-0003308c89be", $data['payment_id']);
        $this->assertSame(10, $data['limit']);
        $this->assertSame("rt_67a4g56e-8487-56d4-a7f3-7yu646s78ec48", $data['cursor']);
        $this->assertEquals("2018-07-18T10:51:18.139Z", $data['created_at_gte']);
        $this->assertEquals("2018-07-18T10:51:18.139Z", $data['created_at_lte']);

        $this->request->setCreatedAt(null);
        $data = $this->request->getData();
        $this->assertFalse(isset($data['created_at_gte']));
        $this->assertFalse(isset($data['created_at_gt']));
        $this->assertFalse(isset($data['created_at_lte']));
        $this->assertFalse(isset($data['created_at_lt']));

        $this->request->setCreatedAt(new DateTime("2018-07-18T10:51:18.139+00:00"), ">=");
        $data = $this->request->getData();
        $this->assertEquals("2018-07-18T10:51:18.139Z", $data['created_at_gte']);

        $this->request->setCreatedAt(new DateTime("2018-07-18T10:51:18.139+00:00"), ">");
        $data = $this->request->getData();
        $this->assertEquals("2018-07-18T10:51:18.139Z", $data['created_at_gt']);

        $this->request->setCreatedAt(new DateTime("2018-07-18T10:51:18.139+00:00"), "<");
        $data = $this->request->getData();
        $this->assertEquals("2018-07-18T10:51:18.139Z", $data['created_at_lt']);

        $this->request->setCreatedAt(new DateTime("2018-07-18T10:51:18.139+00:00"), "<=");
        $data = $this->request->getData();
        $this->assertEquals("2018-07-18T10:51:18.139Z", $data['created_at_lte']);
    }


    public function provider(): array
    {
        return [
            [
                [
                    'shopId' => $this->shopId,
                    'secret' => $this->secretKey,
                    'payment_id' => '215d8da0-000f-50be-b000-0003308c89be',
                    'limit' => 10,
                    'cursor' => "rt_67a4g56e-8487-56d4-a7f3-7yu646s78ec48",
                    "created_at" => new DateTime("2018-07-18T10:51:18.139+00:00")
                ]
            ]
        ];
    }


    public function testEmptyGetData()
    {
        $this->request->initialize([]);

        $data = $this->request->getData();

        $this->assertEmpty($data);
    }

    /**
     * @dataProvider provider
     * @throws ReflectionException
     */
    public function testSendData($provider)
    {
        $this->request->initialize($provider);

        $response = $this->sendMockRequest($this->request, 'receipts.list');
        $this->assertInstanceOf(ReceiptsResponse::class, $response);

        if ($response instanceof ReceiptsResponse) {
            $this->assertTrue($response->isSuccessful());
            $this->assertTrue($response->hasNext());
            $this->assertSame("rt_67a4g56e-8487-56d4-a7f3-7yu646s78ec48", $response->getNext());

            $items = $response->getItems();
            $response = $items[0];
            $this->assertInstanceOf(ReceiptResponse::class, $response);
            $this->assertSame("rt-3da5c87d-0384-50e8-a7f3-8d5646dd9e10", $response->getReceiptId());
            $this->assertTrue($response->isPayment());
            $this->assertTrue($response->isPending());

            $response = $items[1];
            $this->assertInstanceOf(ReceiptResponse::class, $response);
            $this->assertSame("rt-2da5c87d-0384-50e8-a7f3-8d5646dd9e10", $response->getReceiptId());
            $this->assertTrue($response->isPayment());
            $this->assertTrue($response->isSucceeded());

            $response = $items[2];
            $this->assertInstanceOf(ReceiptResponse::class, $response);
            $this->assertSame("rt-4da5c87d-0384-50e8-a7f3-8d5646dd9e10", $response->getReceiptId());
            $this->assertTrue($response->isRefund());
            $this->assertTrue($response->isPending());
        }
    }
}