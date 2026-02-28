<?php

namespace App\Payment\DTO;

/**
 * PaymentRequest Data Transfer Object
 *
 * An immutable object that encapsulates all payment request data.
 * This DTO is created by the PaymentRequestBuilder and passed to
 * payment processors.
 *
 * Using a DTO provides:
 * - Type safety for payment data
 * - Clear contract between builder and processor
 * - Immutable data structure (read-only after creation)
 */
class PaymentRequest
{
    /**
     * Create a new PaymentRequest instance.
     *
     * @param float $amount The payment amount in dollars
     * @param string $currency The 3-letter ISO currency code
     * @param string|null $description Optional payment description
     * @param string|null $customerId Optional customer identifier
     * @param array|null $metadata Optional additional metadata
     */
    public function __construct(
        private float $amount,
        private string $currency,
        private ?string $description = null,
        private ?string $customerId = null,
        private ?array $metadata = null
    ) {}

    /**
     * Get the payment amount.
     *
     * @return float The amount in dollars
     */
    public function getAmount(): float
    {
        return $this->amount;
    }

    /**
     * Get the currency code.
     *
     * @return string The 3-letter ISO currency code
     */
    public function getCurrency(): string
    {
        return $this->currency;
    }

    /**
     * Get the payment description.
     *
     * @return string|null The description or null if not set
     */
    public function getDescription(): ?string
    {
        return $this->description;
    }

    /**
     * Get the customer ID.
     *
     * @return string|null The customer ID or null if not set
     */
    public function getCustomerId(): ?string
    {
        return $this->customerId;
    }

    /**
     * Get the metadata.
     *
     * @return array|null The metadata array or null if not set
     */
    public function getMetadata(): ?array
    {
        return $this->metadata;
    }

    /**
     * Convert the payment request to an associative array.
     *
     * Useful for serialization or API calls.
     *
     * @return array<string, mixed> The payment data as an array
     */
    public function toArray(): array
    {
        return [
            'amount' => $this->amount,
            'currency' => $this->currency,
            'description' => $this->description,
            'customerId' => $this->customerId,
            'metadata' => $this->metadata,
        ];
    }
}
