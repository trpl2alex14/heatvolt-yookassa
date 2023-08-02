<?php

namespace Omnipay\YooKassa;


use Omnipay\Common\Exception\InvalidRequestException;

class Customer
{
    private array $parameters = [];


    public function __construct($fields)
    {
        $this->parameters =  array_intersect_key($fields, array_flip(['full_name', 'inn', 'phone', 'email']));
    }


    public function setFullName(string $value): self
    {
        $this->parameters['full_name'] = $value;

        return $this;
    }


    public function setInn(string $value): self
    {
        $this->parameters['inn'] = $value;

        return $this;
    }


    public function setPhone(string $value): self
    {
        $this->parameters['phone'] = $value;

        return $this;
    }


    public function setEmail(string $value): self
    {
        $this->parameters['email'] = $value;

        return $this;
    }


    public function toArray(): array
    {
        return array_filter($this->parameters, 'trim');
    }

    /**
     * @throws InvalidRequestException
     */
    public function validated(): array
    {
        $customer = $this->toArray();

        if (!isset($customer['phone']) && !(isset($customer['email']) && isset($customer['full_name']))) {
            throw new InvalidRequestException("The phone or email parameter is required");
        }

        if (isset($customer['phone'])) {
            $phone = preg_replace('/[^0-9]/', '', $customer['phone']);
            if (strlen($phone) !== 11) {
                throw new InvalidRequestException("The phone parameter invalid format");
            }
        }

        if (isset($customer['email']) && !filter_var($customer['email'], FILTER_VALIDATE_EMAIL)) {
            throw new InvalidRequestException("The email parameter invalid format");
        }

        return $customer;
    }
}
