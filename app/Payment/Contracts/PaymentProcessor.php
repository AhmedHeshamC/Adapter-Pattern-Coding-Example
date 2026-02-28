<?php

namespace App\Payment\Contracts;

/**
 * PaymentProcessor Interface
 *
 * This is the Target interface that our application uses for processing payments.
 * All payment gateways must implement this interface to be used interchangeably.
 */
interface PaymentProcessor
{
    /**
     * Process a payment for the given amount.
     *
     * @param float $amount The payment amount in dollars (e.g., 10.50 = $10.50)
     * @return bool True if payment was successful, false otherwise
     */
    public function processPayment(float $amount): bool;
}
