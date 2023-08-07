<?php

namespace Omnipay\YooKassa\Message;

use Omnipay\Common\Message\AbstractResponse;
use YooKassa\Request\Receipts\ReceiptResponseInterface;
use YooKassa\Request\Receipts\ReceiptsResponse as YooKassaReceiptsResponse;


class ReceiptsResponse extends AbstractResponse
{
    public function getData(): YooKassaReceiptsResponse
    {
        return $this->data;
    }


    public function isSuccessful()
    {
        return $this->getData()->getType() === 'list';
    }


    public function getNext()
    {
        return $this->getData()->getNextCursor();
    }


    public function hasNext()
    {
        return $this->getData()->hasNextCursor();
    }


    /**
     * @return ReceiptResponse[]
     */
    public function getItems(): array
    {
        $items = $this->getData()->getItems();
        if (!is_array($items) || count($items) === 0) {
            return [];
        }

        return array_map(function (ReceiptResponseInterface $item) {
            return new ReceiptResponse($this->getRequest(), $item);
        }, $items);
    }
}