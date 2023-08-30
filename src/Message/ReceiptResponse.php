<?php

namespace Omnipay\YooKassa\Message;

use DateTime;
use Omnipay\Common\Message\AbstractResponse;
use Omnipay\YooKassa\Item;
use YooKassa\Model\ReceiptRegistrationStatus;
use YooKassa\Model\ReceiptType;
use YooKassa\Model\SettlementInterface;
use YooKassa\Request\Receipts\AbstractReceiptResponse;
use YooKassa\Request\Receipts\ReceiptResponseItem;


class ReceiptResponse extends AbstractResponse
{
    public function getData(): AbstractReceiptResponse
    {
        return $this->data;
    }


    public function isSuccessful()
    {
        return !!$this->getData()->getId();
    }


    public function getReceiptId()
    {
        return $this->getData()->getId();
    }


    public function isPayment()
    {
        return $this->getData()->getType() === ReceiptType::PAYMENT;
    }


    public function isRefund()
    {
        return $this->getData()->getType() === ReceiptType::REFUND;
    }


    public function isSucceeded()
    {
        return $this->getData()->getStatus() === ReceiptRegistrationStatus::SUCCEEDED;
    }


    public function isPending()
    {
        return $this->getData()->getStatus() === ReceiptRegistrationStatus::PENDING;
    }


    public function isCancelled()
    {
        return $this->getData()->getStatus() === ReceiptRegistrationStatus::CANCELED;
    }


    public function getStatus()
    {
        return $this->getData()->getStatus();
    }


    public function getPaymentId()
    {
        return $this->getData()->getPaymentId();
    }


    public function getFiscalDocumentNumber()
    {
        return $this->getData()->getFiscalDocumentNumber();
    }


    public function getFiscalStorageNumber()
    {
        return $this->getData()->getFiscalStorageNumber();
    }


    public function getFiscalAttribute()
    {
        return $this->getData()->getFiscalAttribute();
    }


    public function getFiscalProviderId()
    {
        return $this->getData()->getFiscalProviderId();
    }


    public function getRegisteredDate(): DateTime
    {
        return $this->getData()->getRegisteredAt();
    }


    public function getAmount(): float
    {
        $settlements = $this->getData()->getSettlements();

        $amount = 0;

        if (is_array($settlements)) {
            $amount = array_reduce($settlements, function ($sum, SettlementInterface $item) {
                return $sum + round($item->getAmount()->getIntegerValue() / 100, 2);
            });
        }

        return $amount ?: 0;
    }

    /**
     * @return ?Item[]
     */
    public function getItems(): ?array
    {
        $receiptItems = $this->getData()->getItems();

        if (!is_array($receiptItems)) {
            return null;
        }

        return array_map(function (ReceiptResponseItem $item) {
            return (new Item())
                ->setVatCode($item->getVatCode())
                ->setPaymentMode($item->getPaymentMode())
                ->setPaymentSubject($item->getPaymentSubject())
                ->setDescription($item->getDescription())
                ->setPrice(round($item->getAmount() / 100, 2))
                ->setQuantity($item->getQuantity());
        }, $receiptItems);
    }
}