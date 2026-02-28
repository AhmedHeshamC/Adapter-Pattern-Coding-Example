<?php

namespace App\Payment\Processors;

use App\Payment\Contracts\PaymentProcessor;

/**
 * PayPalProcessor
 *
 * A native implementation of the PaymentProcessor interface for PayPal.
 * Unlike Stripe, this processor doesn't need an adapter because it already
 * uses the expected interface (amount in dollars).
 *
 * This demonstrates that not all payment gateways need adapters - only those
 * with incompatible interfaces do.
 */
class PayPalProcessor implements PaymentProcessor
{
    /**
     * Process a payment through PayPal.
     *
     * PayPal's API natively accepts amounts in dollars, so no conversion
     * is needed - this is a direct implementation.
     *
     * @param float $amount The payment amount in dollars (e.g., 10.50 = $10.50)
     * @return bool True if payment was successful, false otherwise
     */
    public function processPayment(float $amount): bool
    {
        // In a real implementation, this would call PayPal's API
        // For simulation, we consider any positive amount as successful
        return $amount > 0;
    }
}
