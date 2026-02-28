<?php

namespace App\Payment\Builder;

use App\Payment\DTO\PaymentRequest;
use InvalidArgumentException;

/**
 * PaymentRequestBuilder
 *
 * A Builder pattern implementation for constructing PaymentRequest objects.
 * This provides a fluent interface for setting payment details and ensures
 * validation before the request is created.
 *
 * Benefits of the Builder Pattern:
 * - Fluent, readable interface for complex object creation
 * - Validation centralized in one place
 * - Handles optional parameters elegantly
 * - Immutable resulting object (PaymentRequest)
 *
 * Usage:
 *   $request = (new PaymentRequestBuilder())
 *       ->setAmount(10.50)
 *       ->setCurrency('USD')
 *       ->setDescription('Test payment')
 *       ->build();
 */
class PaymentRequestBuilder
{
    /**
     * The payment amount in dollars.
     */
    private float $amount = 0.0;

    /**
     * The 3-letter ISO currency code.
     */
    private string $currency = 'USD';

    /**
     * Optional payment description.
     */
    private ?string $description = null;

    /**
     * Optional customer identifier.
     */
    private ?string $customerId = null;

    /**
     * Optional metadata array.
     */
    private ?array $metadata = null;

    /**
     * Set the payment amount.
     *
     * @param float $amount The amount in dollars (must be non-negative)
     * @return self Returns the builder for method chaining
     */
    public function setAmount(float $amount): self
    {
        $this->amount = $amount;
        return $this;
    }

    /**
     * Set the currency code.
     *
     * @param string $currency The 3-letter ISO currency code (e.g., 'USD', 'EUR')
     * @return self Returns the builder for method chaining
     */
    public function setCurrency(string $currency): self
    {
        $this->currency = $currency;
        return $this;
    }

    /**
     * Set the payment description.
     *
     * @param string $description A description of the payment
     * @return self Returns the builder for method chaining
     */
    public function setDescription(string $description): self
    {
        $this->description = $description;
        return $this;
    }

    /**
     * Set the customer ID.
     *
     * @param string $customerId The customer identifier
     * @return self Returns the builder for method chaining
     */
    public function setCustomerId(string $customerId): self
    {
        $this->customerId = $customerId;
        return $this;
    }

    /**
     * Set the metadata.
     *
     * @param array $metadata Additional metadata key-value pairs
     * @return self Returns the builder for method chaining
     */
    public function setMetadata(array $metadata): self
    {
        $this->metadata = $metadata;
        return $this;
    }

    /**
     * Build and validate the PaymentRequest object.
     *
     * This method validates all set values and throws an exception
     * if any validation fails. If validation passes, it creates
     * and returns a new PaymentRequest instance.
     *
     * @return PaymentRequest The validated payment request
     * @throws InvalidArgumentException If validation fails
     */
    public function build(): PaymentRequest
    {
        $this->validate();

        return new PaymentRequest(
            amount: $this->amount,
            currency: $this->currency,
            description: $this->description,
            customerId: $this->customerId,
            metadata: $this->metadata
        );
    }

    /**
     * Validate all set values.
     *
     * @throws InvalidArgumentException If any validation fails
     */
    private function validate(): void
    {
        // Validate amount is non-negative
        if ($this->amount < 0) {
            throw new InvalidArgumentException('Amount cannot be negative');
        }

        // Validate currency is not empty
        if (empty($this->currency)) {
            throw new InvalidArgumentException('Currency cannot be empty');
        }

        // Validate currency format (3 letters)
        if (!preg_match('/^[A-Z]{3}$/', $this->currency)) {
            throw new InvalidArgumentException(
                'Currency must be a valid 3-letter ISO code'
            );
        }
    }

    /**
     * Reset the builder to default values.
     *
     * Useful when reusing the builder for multiple requests.
     *
     * @return self Returns the builder for method chaining
     */
    public function reset(): self
    {
        $this->amount = 0.0;
        $this->currency = 'USD';
        $this->description = null;
        $this->customerId = null;
        $this->metadata = null;

        return $this;
    }
}
