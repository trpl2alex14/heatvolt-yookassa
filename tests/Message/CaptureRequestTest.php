<?php

namespace Omnipay\YooKassa\Tests\Message;

use Omnipay\Common\Exception\InvalidRequestException;
use Omnipay\YooKassa\Message\CaptureRequest;


class CaptureRequestTest extends PurchaseRequestTest
{
    protected string $requestName = CaptureRequest::class;

    /**
     * @dataProvider provider
     * @throws InvalidRequestException
     */
    public function testGetData($parameters, $customer, $item)
    {
        $parameters['paymentId'] = '22e12f66-000f-5000-8000-18db351245c7';

        $this->request->initialize($parameters);

        $data = $this->request->getData();

        $this->assertEquals($parameters['amount'], $data['amount']['value']);
        $this->assertSame($this->currency, $data['amount']['currency']);

        $this->assertEquals($customer, $data['receipt']['customer']);

        $this->assertEquals($item['price'], $data['receipt']['items'][0]['amount']['value']);
    }


    /**
     * @dataProvider provider
     * @throws \ReflectionException
     */
    public function testSendData($parameters)
    {
        $parameters['paymentId'] = '22e12f66-000f-5000-8000-18db351245c7';
        parent::testSendData($parameters);
    }
}