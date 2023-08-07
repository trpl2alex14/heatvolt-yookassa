<?php

namespace Omnipay\YooKassa\Message;

use DateTime;
use DateTimeInterface;
use Omnipay\YooKassa\Trait\PaymentIdParametersTrait;


class ReceiptsRequest extends Request
{
    use PaymentIdParametersTrait;

    protected ?string $method = 'getReceipts';

    protected string $responseClass = ReceiptsResponse::class;

    protected bool $idempotencyRequest = false;

    protected bool $needId = false;


    public function setLimit($value)
    {
        return $this->setParameter('limit', $value);
    }


    public function getLimit()
    {
        return $this->getParameter('limit');
    }


    public function setCursor($value)
    {
        return $this->setParameter('cursor', $value);
    }


    public function getCursor()
    {
        return $this->getParameter('cursor');
    }


    public function setStatus($value)
    {
        return $this->setParameter('status', $value);
    }


    public function getStatus()
    {
        return $this->getParameter('status');
    }


    public function setCreatedAt(?DateTime $value, $operator = '=')
    {
        if(is_null($value)){
            return $this->setParameter('created_at', []);
        }

        if (str_contains($operator, '>')) {
            $prefix = 'gt';
        } elseif (str_contains($operator, '<')) {
            $prefix = 'lt';
        } else {
            return $this->setParameter('created_at', [
                'created_at_gte' => $value->format(YOOKASSA_DATE),
                'created_at_lte' => $value->format(YOOKASSA_DATE),
            ]);
        }

        if (str_contains($operator, '=')) {
            $prefix = $prefix . 'e';
        }

        $old = $this->getCreatedAt() ?: [];

        $old['created_at_' . $prefix] = $value->format(YOOKASSA_DATE);

        return $this->setParameter('created_at', $old);
    }


    public function getCreatedAt(): array
    {
        return $this->getParameter('created_at') ?: [];
    }


    public function getData()
    {
        return array_filter([
            'payment_id' => $this->getPaymentId(),
            'limit' => $this->getLimit(),
            'cursor' => $this->getCursor(),
            'status' => $this->getStatus(),
            ...$this->getCreatedAt()
        ]);
    }
}