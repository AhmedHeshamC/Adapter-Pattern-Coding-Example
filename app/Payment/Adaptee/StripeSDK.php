<?php

namespace App\Payment\Adaptee;

/**
 * StripeSDK (Adaptee)
 *
 * This class simulates the external Stripe SDK with an incompatible interface.
 * In a real scenario, this would be a third-party library that we cannot modify.
 *
 * Key incompatibility: Uses cents instead of dollars, and has a different method name.
 */
class StripeSDK
{
    /**
     * Make a charge using Stripe's API.
     *
     * NOTE: This method expects the amount in CENTS, not dollars.
     * This is the incompatible interface that our Adapter needs to translate.
     *
     * @param int $valueInCents The charge amount in cents (e.g., 1050 = $10.50)
     * @return int The charged amount in cents
     */
    public function makeCharge(int $valueInCents): int
    {
        // In a real implementation, this would make an API call to Stripe
        // For simulation purposes, we just return the amount charged
        return $valueInCents;
    }
}
