<?php

namespace Unusualdope\LaravelEcommerce\Payment;

use Omnipay\Common\GatewayInterface;
use Omnipay\Omnipay;
use Unusualdope\LaravelEcommerce\Models\Payment\PaymentGateway as PaymentGatewayModel;

abstract class BasePaymentGateway
{
    public string $type = 'omnipay';

    protected ?GatewayInterface $gateway = null;

    public function __construct(protected PaymentGatewayModel $model)
    {
        if ($this->type === 'omnipay') {
            $this->gateway = Omnipay::create($this->getDriver());
            $this->gateway->initialize($this->getConfig());
        }
    }

    /**
     * Get the Omnipay driver name.
     */
    abstract public function getDriver(): string;

    /**
     * Get the default configuration for this gateway.
     */
    abstract public static function getDefaultConfig(): array;

    /**
     * Get the human-readable name of the gateway.
     */
    abstract public static function getName(): string;

    /**
     * Get the unique slug for the gateway.
     */
    public static function getSlug(): string
    {
        return str(static::getName())->slug()->toString();
    }

    /**
     * Prepare configuration for Omnipay.
     */
    protected function getConfig(): array
    {
        return $this->model->config ?? [];
    }

    /**
     * Handle the purchase request.
     */
    abstract public function purchase(array $parameters);

    /**
     * Handle the completion of the purchase.
     */
    abstract public function completePurchase(array $parameters);

    /**
     * Accept and verify a notification (webhook).
     */
    abstract public function acceptNotification(\Illuminate\Http\Request $request);

    /**
     * Get the underlying Omnipay gateway instance.
     */
    public function getOmnipayGateway(): ?GatewayInterface
    {
        return $this->gateway;
    }
}
