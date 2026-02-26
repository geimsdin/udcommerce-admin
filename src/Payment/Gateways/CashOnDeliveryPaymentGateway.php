<?php

namespace Unusualdope\LaravelEcommerce\Payment\Gateways;

use Unusualdope\LaravelEcommerce\Payment\BasePaymentGateway;

class CashOnDeliveryPaymentGateway extends BasePaymentGateway
{
    public string $type = 'offline';

    public function getFee(): float
    {
        return (float) ($this->model->config['fee'] ?? 3.99);
    }

    public function getDriver(): string
    {
        return 'Manual';
    }

    public static function getName(): string
    {
        return 'Contrassegno';
    }

    public static function getDefaultConfig(): array
    {
        return [
            'fee' => 3.99,
        ];
    }

    public function purchase(array $parameters)
    {
        // For COD, purchase is always successful from the gateway perspective
        return new class {
            public function isSuccessful()
            {
                return true;
            }
            public function isRedirect()
            {
                return false;
            }
            public function getTransactionReference()
            {
                return 'COD-' . uniqid();
            }
            public function getMessage()
            {
                return 'Success';
            }
        };
    }

    public function completePurchase(array $parameters)
    {
        return $this->purchase($parameters);
    }

    public function acceptNotification(\Illuminate\Http\Request $request)
    {
        return null;
    }
}
