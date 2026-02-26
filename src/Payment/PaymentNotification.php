<?php

namespace Unusualdope\LaravelEcommerce\Payment;

use Omnipay\Common\Message\NotificationInterface;

class PaymentNotification implements NotificationInterface
{
    public function __construct(
        protected array $data,
        protected string $transactionReference,
        protected string $status,
        protected string $message = ''
    ) {
    }

    public function getTransactionReference(): string
    {
        return $this->transactionReference;
    }

    public function getTransactionStatus(): string
    {
        return $this->status;
    }

    public function getMessage(): string
    {
        return $this->message;
    }

    public function getData(): array
    {
        return $this->data;
    }
}
