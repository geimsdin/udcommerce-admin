<?php

namespace Unusualdope\LaravelEcommerce\Payment\Gateways;

use Unusualdope\LaravelEcommerce\Payment\BasePaymentGateway;

class BankWirePaymentGateway extends BasePaymentGateway
{
    public string $type = 'offline';

    public function getBankDetails(): string
    {
        return $this->model->config['bank_details'] ?? '';
    }

    public function getFinishMessage(): string
    {
        return $this->model->config['finish_message'] ?? '';
    }

    public function getDriver(): string
    {
        return 'Manual';
    }

    public static function getName(): string
    {
        return 'Bonifico Bancario';
    }

    public static function getDefaultConfig(): array
    {
        return [
            'bank_details' => "IBAN: IT12 X 01234 01234 000000123456\nIntestato a: Libroso SRL",
            'finish_message' => "Si prega di inserire il numero d'ordine nella causale del bonifico. L'ordine verrà elaborato alla ricezione del pagamento.",
        ];
    }

    public function purchase(array $parameters)
    {
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
                return 'WIRE-' . strtoupper(uniqid());
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
