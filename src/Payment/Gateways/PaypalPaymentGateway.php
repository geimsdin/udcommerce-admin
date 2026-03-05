<?php

namespace Unusualdope\LaravelEcommerce\Payment\Gateways;

use Unusualdope\LaravelEcommerce\Payment\BasePaymentGateway;

class PaypalPaymentGateway extends BasePaymentGateway
{
    public string $type = 'omnipay';

    public function getDriver(): string
    {
        return 'PayPal_Rest';
    }

    public static function getName(): string
    {
        return 'PayPal';
    }

    public static function getDefaultConfig(): array
    {
        return [
            'clientId' => '',
            'secret' => '',
            'webhookId' => '',
            'testMode' => true,
        ];
    }

    public function purchase(array $parameters)
    {
        return $this->gateway->purchase($parameters)->send();
    }

    public function completePurchase(array $parameters)
    {
        return $this->gateway->completePurchase($parameters)->send();
    }

    public function acceptNotification(\Illuminate\Http\Request $request)
    {
        $parameters = $request->all();
        $webhookId = $this->model->config['webhookId'] ?? '';

        if (!empty($webhookId)) {
            $this->verifyPaypalSignature($request, $webhookId);
        }

        $transactionReference = $parameters['resource']['id'] ?? '';
        $paypalStatus = $parameters['resource']['state'] ?? '';

        $status = \Omnipay\Common\Message\NotificationInterface::STATUS_FAILED;
        if ($paypalStatus === 'completed' || $paypalStatus === 'approved') {
            $status = \Omnipay\Common\Message\NotificationInterface::STATUS_COMPLETED;
        }

        return new \Unusualdope\LaravelEcommerce\Payment\PaymentNotification(
            $parameters,
            $transactionReference,
            $status,
            "PayPal Webhook: " . ($parameters['event_type'] ?? 'unknown')
        );
    }

    protected function verifyPaypalSignature(\Illuminate\Http\Request $request, string $webhookId): void
    {
        $headers = $request->headers;

        $payload = [
            'auth_algo' => $headers->get('PAYPAL-AUTH-ALGO'),
            'cert_url' => $headers->get('PAYPAL-CERT-URL'),
            'transmission_id' => $headers->get('PAYPAL-TRANSMISSION-ID'),
            'transmission_sig' => $headers->get('PAYPAL-TRANSMISSION-SIG'),
            'transmission_time' => $headers->get('PAYPAL-TRANSMISSION-TIME'),
            'webhook_id' => $webhookId,
            'webhook_event' => $request->all(),
        ];

        $clientId = $this->model->config['clientId'] ?? '';
        $secret = $this->model->config['secret'] ?? '';
        $isTest = $this->model->config['testMode'] ?? true;

        $baseUrl = $isTest ? 'https://api.sandbox.paypal.com' : 'https://api.paypal.com';

        // We need an OAuth token to call the verification endpoint
        $authResponse = \Illuminate\Support\Facades\Http::asForm()
            ->withBasicAuth($clientId, $secret)
            ->post($baseUrl . '/v1/oauth2/token', [
                'grant_type' => 'client_credentials',
            ]);

        if (!$authResponse->successful()) {
            throw new \Exception("PayPal Webhook: Failed to authenticate for signature verification.");
        }

        $accessToken = $authResponse->json('access_token');

        $verifyResponse = \Illuminate\Support\Facades\Http::withToken($accessToken)
            ->post($baseUrl . '/v1/notifications/verify-webhook-signature', $payload);

        if (!$verifyResponse->successful() || $verifyResponse->json('verification_status') !== 'SUCCESS') {
            throw new \Exception("PayPal Webhook: Signature verification failed.");
        }
    }
}
