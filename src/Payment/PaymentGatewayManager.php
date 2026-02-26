<?php

namespace Unusualdope\LaravelEcommerce\Payment;

use Illuminate\Support\Collection;
use Unusualdope\LaravelEcommerce\Payment\Gateways\PaypalPaymentGateway;
use Unusualdope\LaravelEcommerce\Payment\Gateways\StripePaymentGateway;
use Unusualdope\LaravelEcommerce\Models\Payment\PaymentGateway as PaymentGatewayModel;

class PaymentGatewayManager
{
    /**
     * List of registered gateway classes.
     */
    protected array $gateways = [
        PaypalPaymentGateway::class,
        StripePaymentGateway::class,
        \Unusualdope\LaravelEcommerce\Payment\Gateways\CashOnDeliveryPaymentGateway::class,
        \Unusualdope\LaravelEcommerce\Payment\Gateways\BankWirePaymentGateway::class,
    ];

    /**
     * Sync defined gateways with the database.
     */
    public function syncGateways(): void
    {
        foreach ($this->gateways as $gatewayClass) {
            $slug = $gatewayClass::getSlug();

            $model = PaymentGatewayModel::updateOrCreate(
                ['slug' => $slug],
                [
                    'name' => $gatewayClass::getName(),
                    'driver' => (new $gatewayClass(new PaymentGatewayModel()))->getDriver(),
                ]
            );

            if (empty($model->config)) {
                $model->update(['config' => $gatewayClass::getDefaultConfig()]);
            }
        }
    }

    /**
     * Get all active gateways from DB.
     */
    public function getActiveGateways(): Collection
    {
        return PaymentGatewayModel::where('active', true)->get()->map(fn(PaymentGatewayModel $model) => $this->resolveGateway($model))->filter();
    }

    /**
     * Resolve a gateway instance from a model.
     */
    public function resolveGateway(PaymentGatewayModel $model): ?BasePaymentGateway
    {
        foreach ($this->gateways as $gatewayClass) {
            if ($gatewayClass::getSlug() === $model->slug) {
                return new $gatewayClass($model);
            }
        }

        return null;
    }

    /**
     * Get a gateway by slug.
     */
    public function getGatewayBySlug(string $slug): ?BasePaymentGateway
    {
        $model = PaymentGatewayModel::where('slug', $slug)->first();
        if (!$model) {
            return null;
        }

        return $this->resolveGateway($model);
    }
}
