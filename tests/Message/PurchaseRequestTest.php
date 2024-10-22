<?php

namespace Omnipay\YooKassa\Tests\Message;

use Omnipay\YooKassa\Message\PaymentResponse;
use Omnipay\YooKassa\Message\PurchaseRequest;
use ReflectionException;
use YooKassa\Request\Payments\Locale;


class PurchaseRequestTest extends AbstractRequestTest
{
    protected string $requestName = PurchaseRequest::class;

    /**
     * @dataProvider provider
     */
    public function testGetData($parameters, $customer, $item)
    {
        $this->request->initialize($parameters);

        $data = $this->request->getData();

        $this->assertEquals($parameters['amount'], $data['amount']['value']);
        $this->assertSame($this->currency, $data['amount']['currency']);
        $this->assertSame($parameters['description'], $data['description']);
        $this->assertSame($this->returnUrl, $data['confirmation']['return_url']);
        $this->assertSame(Locale::RUSSIAN, $data['confirmation']['locale']);
        if (isset($parameters['transactionId'])) {
            $this->assertSame($parameters['transactionId'], $data['metadata']['transactionId']);
        } else {
            $this->assertFalse(isset($data['metadata']['transactionId']));
        }

        $this->assertEquals($customer, $data['receipt']['customer']);

        $this->assertEquals($item['price'], $data['receipt']['items'][0]['amount']['value']);
        $this->assertSame($item['description'], $data['receipt']['items'][0]['description']);
        $this->assertSame($item['quantity'], $data['receipt']['items'][0]['quantity']);
        $this->assertSame($item['paymentSubject'], $data['receipt']['items'][0]['payment_subject']);
        $this->assertSame($item['paymentMode'], $data['receipt']['items'][0]['payment_mode']);
        $this->assertSame($this->currency, $data['receipt']['items'][0]['amount']['currency']);
        $this->assertSame($this->vatCode, $data['receipt']['items'][0]['vat_code']);
    }

    /**
     * @dataProvider provider
     * @throws ReflectionException
     */
    public function testSendData($parameters)
    {
        $this->request->initialize($parameters);

        $response = $this->sendMockRequest($this->request, 'payment.pending');
        $this->assertInstanceOf(PaymentResponse::class, $response);

        $this->assertTrue($response->isSuccessful());
        $this->assertTrue($response->isRedirect());
        $this->assertTrue($response->isPending());
        $this->assertSame($response->getRedirectUrl(), $this->confirmUrl);
    }

    /**
     * @dataProvider providerBadData
     */
    public function testBadGetData($parameters, $exception)
    {
        $this->expectException($exception);

        $this->request->initialize($parameters);

        $this->request->getData();
    }
}