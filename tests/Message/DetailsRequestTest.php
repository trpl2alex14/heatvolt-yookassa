<?php

namespace Omnipay\YooKassa\Tests\Message;

use DateTime;
use Omnipay\Common\Exception\InvalidRequestException;
use Omnipay\YooKassa\Message\DetailsRequest;
use Omnipay\YooKassa\Message\PaymentResponse;
use ReflectionException;


class DetailsRequestTest extends AbstractRequestTest
{
    protected string $requestName = DetailsRequest::class;

    private string $paymentId = '22e12f66-000f-5000-8000-18db351245c7';


    public function testGetData()
    {
        $this->request->initialize(['paymentId' => $this->paymentId]);

        $data = $this->request->getData();

        $this->assertSame($this->paymentId, $data);
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
        $this->request->initialize(['paymentId' => $this->paymentId]);

        $response = $this->sendMockRequest($this->request, 'payment.pending');
        $this->assertInstanceOf(PaymentResponse::class, $response);

        if($response instanceof PaymentResponse) {
            $this->assertTrue($response->isSuccessful());
            $this->assertSame($this->paymentId, $response->getPaymentId());
            $this->assertFalse($response->isPaid());
            $this->assertSame("sdfdsfdsf34234", $response->getTransactionId());
            $this->assertTrue($response->isRedirect());
            $this->assertTrue($response->isPending());
            $this->assertSame($this->confirmUrl, $response->getRedirectUrl());
            $this->assertEquals(155, $response->getAmount());
            $this->assertSame($this->currency, $response->getCurrency());
            $this->assertEquals(new DateTime("2018-07-18T10:51:18.139Z"), $response->getPaymentDate());
            $this->assertSame("bank_card", $response->getPayer());
            $this->assertNull( $response->getReceiptStatus());
        }
    }
}