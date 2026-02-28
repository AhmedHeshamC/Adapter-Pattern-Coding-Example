<?php

namespace App\Payment\Factory;

use App\Payment\Contracts\PaymentProcessor;
use App\Payment\Adapter\StripeAdapter;
use App\Payment\Adaptee\StripeSDK;
use App\Payment\Processors\PayPalProcessor;
use InvalidArgumentException;

/**
 * PaymentGatewayFactory
 *
 * A Factory class that creates and returns the appropriate PaymentProcessor
 * based on a provider string. This hides the complexity of which adapter
 * or processor is being used from the client code.
 *
 * Benefits:
 * - Client code doesn't need to know about specific implementations
 * - Easy to add new payment providers without modifying client code
 * - Centralized point for configuring payment processors
 *
 * Usage:
 *   $processor = PaymentGatewayFactory::getProcessor('stripe');
 *   $processor->processPayment(10.50);
 */
class PaymentGatewayFactory
{
    /**
     * Supported payment providers.
     */
    public const PROVIDER_STRIPE = 'STRIPE';
    public const PROVIDER_PAYPAL = 'PAYPAL';

    /**
     * Get a PaymentProcessor instance for the specified provider.
     *
     * This static factory method creates and returns the appropriate
     * payment processor based on the provider string. It handles all
     * the complexity of instantiating the correct class with its
     * dependencies.
     *
     * @param string $provider The payment provider name (e.g., 'stripe', 'paypal')
     * @return PaymentProcessor The configured payment processor
     * @throws InvalidArgumentException If the provider is not supported
     */
    public static function getProcessor(string $provider): PaymentProcessor
    {
        // Normalize the provider name to uppercase for comparison
        $provider = strtoupper($provider);

        return match ($provider) {
            self::PROVIDER_STRIPE => self::createStripeProcessor(),
            self::PROVIDER_PAYPAL => self::createPayPalProcessor(),
            default => throw new InvalidArgumentException(
                "Payment provider '{$provider}' is not supported. " .
                "Supported providers: STRIPE, PAYPAL"
            ),
        };
    }

    /**
     * Create a Stripe payment processor.
     *
     * Stripe requires an adapter because its SDK uses cents instead of dollars.
     * This method encapsulates the creation of both the SDK and the adapter.
     *
     * @return PaymentProcessor The Stripe adapter wrapped around the SDK
     */
    private static function createStripeProcessor(): PaymentProcessor
    {
        // Create the adaptee (third-party SDK)
        $stripeSDK = new StripeSDK();

        // Wrap it in our adapter
        return new StripeAdapter($stripeSDK);
    }

    /**
     * Create a PayPal payment processor.
     *
     * PayPal doesn't require an adapter because it natively uses the same
     * interface we expect (amounts in dollars).
     *
     * @return PaymentProcessor The PayPal processor
     */
    private static function createPayPalProcessor(): PaymentProcessor
    {
        return new PayPalProcessor();
    }

    /**
     * Get a list of all supported payment providers.
     *
     * Useful for validation, UI dropdowns, or documentation.
     *
     * @return array<string> List of supported provider names
     */
    public static function getSupportedProviders(): array
    {
        return [
            self::PROVIDER_STRIPE,
            self::PROVIDER_PAYPAL,
        ];
    }

    /**
     * Check if a provider is supported.
     *
     * @param string $provider The provider name to check
     * @return bool True if the provider is supported
     */
    public static function isProviderSupported(string $provider): bool
    {
        return in_array(strtoupper($provider), self::getSupportedProviders(), true);
    }
}
