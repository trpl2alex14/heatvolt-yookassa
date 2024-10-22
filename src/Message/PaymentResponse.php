<?php

namespace Omnipay\YooKassa\Message;

use DateTime;
use Omnipay\Common\Message\AbstractResponse;
use Omnipay\Common\Message\RedirectResponseInterface;
use YooKassa\Model\Payment\ConfirmationType;
use YooKassa\Model\Payment\PaymentInterface;
use YooKassa\Model\Payment\PaymentStatus;


class PaymentResponse extends AbstractResponse implements RedirectResponseInterface
{
    public function getData(): PaymentInterface
    {
        return $this->data;
    }


    public function isSuccessful()
    {
        return !!$this->getData()->getId();
    }


    public function getPaymentId()
    {
        return $this->getData()->getId();
    }


    public function isPaid()
    {
        return $this->getData()->getPaid();
    }


    public function getTransactionId()
    {
        return $this->getData()->getMetadata()['transactionId'] ?? null;
    }


    public function isRedirect()
    {
        return $this->getData()->getConfirmation()?->getType() === ConfirmationType::REDIRECT;
    }


    public function getRedirectUrl()
    {
        return $this->getData()->getConfirmation()?->getConfirmationUrl();
    }


    public function getRedirectData()
    {
        return $this->getData()->getConfirmation()?->getConfirmationData();
    }


    public function isPending()
    {
        return $this->getData()->getStatus() === PaymentStatus::PENDING;
    }


    public function isCancelled()
    {
        return $this->getData()->getStatus() === PaymentStatus::CANCELED;
    }


    public function getStatus()
    {
        return $this->getData()->getStatus();
    }


    public function getAmount()
    {
        return $this->getData()->getAmount()->getValue();
    }


    public function getCurrency(): string
    {
        return $this->getData()->getAmount()->getCurrency();
    }


    public function getPaymentDate(): DateTime
    {
        return $this->getData()->getCreatedAt();
    }


    public function getPayer(): string
    {
        $method = $this->getData()->getPaymentMethod();

        return $method->getTitle() ?: $method->getType() ?: '';
    }


    public function getPaymentMethod()
    {
        return $this->getData()->getPaymentMethod();
    }


    public function getMetadata()
    {
        return $this->getData()->getMetadata();
    }


    public function getReceiptStatus()
    {
        return $this->getData()->getReceiptRegistration();
    }
}