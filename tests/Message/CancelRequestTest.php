<?php

namespace Omnipay\YooKassa\Tests\Message;

use DateTime;
use Omnipay\Common\Exception\InvalidRequestException;
use Omnipay\YooKassa\Message\CancelRequest;
use Omnipay\YooKassa\Message\PaymentResponse;
use ReflectionException;


class CancelRequestTest extends AbstractRequestTest
{
    protected string $requestName = CancelRequest::class;

    private string $paymentId = '22e12f66-000f-5000-8000-18db351245c7';


    public function testGetData()
    {
        $this->request->initialize(['paymentId' => $this->paymentId]);

        $data = $this->request->getData();

        $this->assertNull($data);
        $this->assertSame($this->paymentId, $this->request->getPaymentId());
        $this->assertSame(md5($this->paymentId), $this->request->getIdempotencyKey());
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

        $response = $this->sendMockRequest($this->request, 'payment.canceled');
        $this->assertInstanceOf(PaymentResponse::class, $response);

        if($response instanceof PaymentResponse) {
            $this->assertTrue($response->isSuccessful());
            $this->assertSame($this->paymentId, $response->getPaymentId());
            $this->assertTrue($response->isCancelled());
        }
    }
}