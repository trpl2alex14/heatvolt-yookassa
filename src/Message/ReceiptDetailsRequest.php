<?php

namespace Omnipay\YooKassa\Message;

use Omnipay\Common\Exception\InvalidRequestException;


class ReceiptDetailsRequest extends Request
{
    protected ?string $method = 'getReceiptInfo';

    protected string $responseClass = ReceiptResponse::class;

    protected bool $idempotencyRequest = false;


    public function setReceiptId($value)
    {
        return $this->setParameter('receiptId', $value);
    }


    public function getReceiptId()
    {
        return $this->getParameter('receiptId');
    }


    public function getId()
    {
        return $this->getParameter('receiptId');
    }

    /**
     * @throws InvalidRequestException
     */
    public function getData()
    {
        $this->validate('receiptId');

        return null;
    }
}