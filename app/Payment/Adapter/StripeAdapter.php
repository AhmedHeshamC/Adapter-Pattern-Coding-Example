<?php

namespace App\Payment\Adapter;

use App\Payment\Contracts\PaymentProcessor;
use App\Payment\Adaptee\StripeSDK;

/**
 * StripeAdapter (Adapter)
 *
 * This class adapts the StripeSDK (Adaptee) to work with our PaymentProcessor
 * interface (Target). It handles the translation between the two incompatible
 * interfaces.
 *
 * Key adaptations:
 * 1. Method mapping: processPayment() -> makeCharge()
 * 2. Data transformation: dollars -> cents (multiply by 100)
 */
class StripeAdapter implements PaymentProcessor
{
    /**
     * The Stripe SDK instance being adapted.
     */
    private StripeSDK $stripeSDK;

    /**
     * Create a new StripeAdapter instance.
     *
     * @param StripeSDK $stripeSDK The Stripe SDK to adapt
     */
    public function __construct(StripeSDK $stripeSDK)
    {
        $this->stripeSDK = $stripeSDK;
    }

    /**
     * Process a payment through Stripe.
     *
     * This method implements the PaymentProcessor interface by:
     * 1. Converting the amount from dollars to cents
     * 2. Delegating to Stripe's makeCharge method
     * 3. Converting the result to a boolean
     *
     * @param float $amount The payment amount in dollars (e.g., 10.50 = $10.50)
     * @return bool True if payment was successful, false otherwise
     */
    public function processPayment(float $amount): bool
    {
        // Step 1: Data transformation (Dollars -> Cents)
        // Using round() to handle floating point precision issues
        $amountInCents = (int) round($amount * 100);

        // Step 2: Method mapping - call the adaptee's method
        $chargedAmount = $this->stripeSDK->makeCharge($amountInCents);

        // Step 3: Convert result to boolean
        // If the charged amount matches what we requested, it was successful
        return $chargedAmount === $amountInCents && $chargedAmount > 0;
    }
}
