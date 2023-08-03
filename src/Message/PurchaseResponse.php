<?php

namespace Omnipay\YooKassa\Message;

use Omnipay\Common\Message\AbstractResponse;
use Omnipay\Common\Message\RedirectResponseInterface;
use YooKassa\Model\ConfirmationType;
use YooKassa\Model\PaymentInterface;
use YooKassa\Model\PaymentStatus;


class PurchaseResponse extends AbstractResponse implements RedirectResponseInterface
{
    public function getData(): PaymentInterface
    {
        return $this->data;
    }


    public function isSuccessful()
    {
        return !!$this->getData()->getId();
    }


    public function getTransactionId()
    {
        return $this->getData()->getId();
    }


    public function getRedirectUrl()
    {
        return $this->getData()->getConfirmation()->getConfirmationUrl();
    }


    public function getRedirectData()
    {
        return $this->getData()->getConfirmation()->getConfirmationData();
    }


    public function isPending()
    {
        return $this->getData()->getStatus() === PaymentStatus::PENDING;
    }


    public function isCancelled()
    {
        return $this->getData()->getStatus() === PaymentStatus::CANCELED;
    }


    public function isRedirect()
    {
        return $this->getData()->getConfirmation()->getType() === ConfirmationType::REDIRECT;
    }
}