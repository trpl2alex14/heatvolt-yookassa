<?php

namespace Omnipay\YooKassa\Message;

use Omnipay\Common\Message\AbstractResponse;
use YooKassa\Common\ListObject;
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
        if(!($items instanceof ListObject)){
            return [];
        }

        $result = [];
        foreach ($items->getItems() as $item) {
            if($item instanceof ReceiptResponseInterface) {
                $result[] = new ReceiptResponse($this->getRequest(), $item);
            }
        }

        return $result;
    }
}