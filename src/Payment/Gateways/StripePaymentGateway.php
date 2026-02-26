<?php

namespace Unusualdope\LaravelEcommerce\Payment\Gateways;

use Illuminate\Support\Facades\Http;
use Unusualdope\LaravelEcommerce\Payment\BasePaymentGateway;

class StripePaymentGateway extends BasePaymentGateway
{
    public string $type = 'redirect';

    public function getDriver(): string
    {
        return 'Stripe_Checkout';
    }

    public static function getName(): string
    {
        return 'Stripe';
    }

    public static function getDefaultConfig(): array
    {
        return [
            'apiKey' => '',
            'publishableKey' => '',
            'testMode' => true,
        ];
    }

    public function purchase(array $parameters)
    {
        $apiKey = $this->model->config['apiKey'] ?? '';

        $response = Http::withToken($apiKey)
            ->asForm()
            ->post('https://api.stripe.com/v1/checkout/sessions', [
                'payment_method_types' => ['card'],
                'line_items' => [
                    [
                        'price_data' => [
                            'currency' => strtolower($parameters['currency'] ?? 'eur'),
                            'product_data' => [
                                'name' => $parameters['description'] ?? 'Order Payment',
                            ],
                            'unit_amount' => (int) (round($parameters['amount'] * 100)),
                        ],
                        'quantity' => 1,
                    ]
                ],
                'mode' => 'payment',
                'success_url' => $parameters['returnUrl'],
                'cancel_url' => $parameters['cancelUrl'],
                'client_reference_id' => $parameters['transactionId'] ?? null,
            ]);

        if (!$response->successful()) {
            throw new \Exception('Stripe error: ' . ($response->json('error.message') ?? 'Unknown error'));
        }

        $session = $response->json();

        return new class ($session) {
            public function __construct(protected $session)
            {}

            public function isSuccessful()
            {
                return false;
            }

            public function isRedirect()
            {
                return true;
            }

            public function getRedirectUrl()
            {
                return $this->session['url'];
            }

            public function getRedirectMethod()
            {
                return 'GET';
            }

            public function getRedirectData()
            {
                return [];
            }

            public function getTransactionReference()
            {
                return $this->session['id'];
            }

            public function getMessage()
            {
                return 'Redirecting to Stripe Checkout';
            }
        };
    }

    public function completePurchase(array $parameters)
    {
        $apiKey = $this->model->config['apiKey'] ?? '';
        $sessionId = $parameters['transactionReference'] ?? null;

        if (!$sessionId) {
            // Try to get it from request if not passed
            $sessionId = request()->query('session_id');
        }

        if (!$sessionId) {
            return new class {
                public function isSuccessful()
                {
                    return false;
                }

                public function getMessage()
                {
                    return 'Missing session ID';
                }

                public function getTransactionReference()
                {
                    return null;
                }
            };
        }

        $response = Http::withToken($apiKey)
            ->get("https://api.stripe.com/v1/checkout/sessions/{$sessionId}");

        if (!$response->successful()) {
            return new class ($response) {
                public function __construct(protected $res)
                {}

                public function isSuccessful()
                {
                    return false;
                }

                public function getMessage()
                {
                    return 'Stripe API error: ' . $this->res->json('error.message');
                }

                public function getTransactionReference()
                {
                    return null;
                }
            };
        }

        $session = $response->json();
        $isPaid = ($session['payment_status'] === 'paid');

        return new class ($isPaid, $session) {
            public function __construct(protected $isPaid, protected $session)
            {}

            public function isSuccessful()
            {
                return $this->isPaid;
            }

            public function getMessage()
            {
                return $this->isPaid ? 'Success' : 'Payment pending/failed';
            }

            public function getTransactionReference()
            {
                return $this->session['payment_intent'] ?? $this->session['id'];
            }
        };
    }

    public function acceptNotification(\Illuminate\Http\Request $request)
    {
        // For webhooks, we could implement verification here if needed
        return null;
    }
}
