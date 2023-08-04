<?php

namespace Omnipay\YooKassa\Message;

use Omnipay\Common\Exception\InvalidRequestException;
use Omnipay\YooKassa\Trait\PaymentIdParametersTrait;


class CancelRequest extends Request
{
    use PaymentIdParametersTrait;

    protected string|null $method = 'cancelPayment';

    /**
     * @throws InvalidRequestException
     */
    public function getData()
    {
        $this->validate('paymentId');

        if(!$this->getIdempotencyKey()){
            $this->setIdempotencyKey(md5($this->getPaymentId()));
        }

        return null;
    }
}