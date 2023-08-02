<?php


namespace Omnipay\YooKassa\Message;


use Omnipay\Common\Message\AbstractResponse;
use Omnipay\Common\Message\RedirectResponseInterface;

class PurchaseResponse extends AbstractResponse implements RedirectResponseInterface
{

    /**
     * @inheritDoc
     */
    public function isSuccessful()
    {
        // TODO: Implement isSuccessful() method.
    }
}