<?php

namespace Omnipay\YooKassa\Tests\Message;

use DateTime;
use Omnipay\Common\Exception\InvalidRequestException;
use Omnipay\YooKassa\Customer;
use Omnipay\YooKassa\Item;
use Omnipay\YooKassa\Message\RefundRequest;
use Omnipay\YooKassa\Message\RefundResponse;
use TypeError;


class RefundRequestTest extends PurchaseRequestTest
{
    protected string $requestName = RefundRequest::class;

    /**
     * @dataProvider provider
     */
    public function testGetData($parameters, $customer, $item)
    {
        $this->request->initialize($parameters);

        $data = $this->request->getData();

        $this->assertSame($parameters['payment_id'], $data['payment_id']);
        $this->assertEquals($parameters['amount'], $data['amount']['value']);
        $this->assertSame($this->currency, $data['amount']['currency']);

        if (isset($parameters['description'])) {
            $this->assertSame($parameters['description'], $data['description']);
        }

        if ($customer && $item) {
            $this->assertEquals($customer, $data['receipt']['customer']);
            $this->assertEquals($item['price'], $data['receipt']['items'][0]['amount']['value']);
        }
    }


    /**
     * @dataProvider provider
     * @throws \ReflectionException
     */
    public function testSendData($parameters)
    {
        $this->request->initialize($parameters);

        $response = $this->sendMockRequest($this->request, 'refund.succeeded');
        $this->assertInstanceOf(RefundResponse::class, $response);

        if ($response instanceof RefundResponse) {
            $this->assertTrue($response->isSuccessful());
            $this->assertTrue($response->isSucceeded());
            $this->assertSame($parameters['payment_id'], $response->getPaymentId());
            $this->assertSame("216749f7-0016-50be-b000-078d43a63ae4", $response->getRefundId());
            $this->assertSame("Test purchase description 1", $response->getDescription());
            $this->assertSame("succeeded", $response->getReceiptStatus());
            $this->assertSame("1.00", $response->getAmount());
            $this->assertSame("succeeded", $response->getStatus());
            $this->assertEquals(new DateTime("2017-10-04T19:27:51.407Z"), $response->getRefundDate());
        }
    }


    public function provider(): array
    {
        return [
            'payment 1' => [
                [
                    'shopId' => $this->shopId,
                    'secret' => $this->secretKey,
                    'payment_id' => '216749f7-0016-50be-b000-078d43a63ae4',
                    'amount' => 155,
                    'currency' => $this->currency,
                    'description' => 'Test purchase description 1',
                    'vatCode' => $this->vatCode,
                    'items' => array_map(fn($item) => new Item($item), $this->items),
                    'customer' => new Customer($this->customer[0])
                ],
                $this->customer[0],
                $this->items[0]
            ],
            'payment 2' => [
                [
                    'shopId' => $this->shopId,
                    'secret' => $this->secretKey,
                    'payment_id' => '216749f7-0016-50be-b000-078d43a63ae4',
                    'amount' => 31,
                    'currency' => $this->currency,
                ],
                null,
                null
            ],
        ];
    }


    public function providerBadData(): array
    {
        return [
            'required amount' => [
                [
                    'payment_id' => '216749f7-0016-50be-b000-078d43a63ae4',
                    'currency' => $this->currency,
                ],
                InvalidRequestException::class
            ],
            'customer type' => [
                [
                    'payment_id' => '216749f7-0016-50be-b000-078d43a63ae4',
                    'amount' => 31,
                    'currency' => $this->currency,
                    'customer' => ['phone' => '123123123'],
                    'items' => array_map(fn($item) => new Item($item), $this->items)
                ],
                TypeError::class
            ],
            'required payment_id' => [
                [
                    'amount' => 31,
                    'currency' => $this->currency,
                ],
                InvalidRequestException::class
            ],
        ];
    }
}