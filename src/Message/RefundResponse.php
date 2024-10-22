<?php

namespace Omnipay\YooKassa\Message;

use DateTime;
use Omnipay\Common\Message\AbstractResponse;
use YooKassa\Model\Refund\RefundInterface;
use YooKassa\Model\Refund\RefundStatus;


class RefundResponse extends AbstractResponse
{
    public function getData(): RefundInterface
    {
        return $this->data;
    }


    public function isSuccessful()
    {
        return !!$this->getData()->getId();
    }


    public function getRefundId()
    {
        return $this->getData()->getId();
    }


    public function getPaymentId()
    {
        return $this->getData()->getPaymentId();
    }


    public function isSucceeded()
    {
        return $this->getData()->getStatus() === RefundStatus::SUCCEEDED;
    }


    public function isPending()
    {
        return $this->getData()->getStatus() === RefundStatus::PENDING;
    }


    public function isCancelled()
    {
        return $this->getData()->getStatus() === RefundStatus::CANCELED;
    }


    public function getStatus()
    {
        return $this->getData()->getStatus();
    }


    public function getReceiptStatus()
    {
        return $this->getData()->getReceiptRegistration();
    }


    public function getRefundDate(): DateTime
    {
        return $this->getData()->getCreatedAt();
    }


    public function getAmount()
    {
        return $this->getData()->getAmount()->getValue();
    }


    public function getCurrency(): string
    {
        return $this->getData()->getAmount()->getCurrency();
    }


    public function getDescription()
    {
        return $this->getData()->getDescription();
    }
}