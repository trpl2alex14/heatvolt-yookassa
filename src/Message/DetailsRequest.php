<?php

namespace Omnipay\YooKassa\Message;

use Omnipay\Common\Exception\InvalidRequestException;


class DetailsRequest extends Request
{
    protected string|null $method = 'getPaymentInfo';

    protected bool $idempotencyRequest = false;


    public function setPaymentId($value)
    {
        return $this->setParameter('paymentId', $value);
    }


    public function getPaymentId()
    {
        return $this->getParameter('paymentId');
    }

    /**
     * @throws InvalidRequestException
     */
    public function getData()
    {
        $this->validate('paymentId');

        return $this->getPaymentId();
    }
}