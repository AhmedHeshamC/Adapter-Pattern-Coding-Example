<?php

namespace Tests\Unit;

use PHPUnit\Framework\TestCase;
use App\Payment\Contracts\PaymentProcessor;
use App\Payment\Adapter\StripeAdapter;
use App\Payment\Adaptee\StripeSDK;

class StripeAdapterTest extends TestCase
{
    /**
     * Test that StripeAdapter implements PaymentProcessor interface.
     */
    public function test_stripe_adapter_implements_payment_processor(): void
    {
        $adapter = new StripeAdapter(new StripeSDK());

        $this->assertInstanceOf(PaymentProcessor::class, $adapter);
    }

    /**
     * Test that StripeAdapter can process a payment.
     */
    public function test_stripe_adapter_can_process_payment(): void
    {
        $adapter = new StripeAdapter(new StripeSDK());

        $result = $adapter->processPayment(10.50);

        $this->assertTrue($result);
    }

    /**
     * Test that StripeAdapter correctly converts dollars to cents.
     * This is the KEY test: $10.50 should become 1050 cents.
     */
    public function test_stripe_adapter_converts_dollars_to_cents(): void
    {
        // Create a mock StripeSDK to verify the conversion
        $mockStripeSDK = $this->createMock(StripeSDK::class);

        // We expect makeCharge to be called with 1050 cents (not 10.50)
        $mockStripeSDK->expects($this->once())
            ->method('makeCharge')
            ->with($this->equalTo(1050))
            ->willReturn(1050);

        $adapter = new StripeAdapter($mockStripeSDK);
        $adapter->processPayment(10.50);
    }

    /**
     * Test conversion of various dollar amounts to cents.
     *
     * @dataProvider dollarToCentsProvider
     */
    public function test_stripe_adapter_converts_various_amounts(float $dollars, int $expectedCents): void
    {
        $mockStripeSDK = $this->createMock(StripeSDK::class);

        $mockStripeSDK->expects($this->once())
            ->method('makeCharge')
            ->with($this->equalTo($expectedCents))
            ->willReturn($expectedCents);

        $adapter = new StripeAdapter($mockStripeSDK);
        $adapter->processPayment($dollars);
    }

    /**
     * Data provider for dollar to cents conversion tests.
     */
    public static function dollarToCentsProvider(): array
    {
        return [
            '10.50 dollars' => [10.50, 1050],
            '1.00 dollar' => [1.00, 100],
            '0.01 dollar (1 cent)' => [0.01, 1],
            '0.99 dollars' => [0.99, 99],
            '100.00 dollars' => [100.00, 10000],
            '99.99 dollars' => [99.99, 9999],
            '0.00 dollars (zero)' => [0.00, 0],
            '1000.50 dollars' => [1000.50, 100050],
        ];
    }

    /**
     * Test that StripeAdapter handles fractional cents correctly (rounding).
     */
    public function test_stripe_adapter_handles_fractional_cents(): void
    {
        $mockStripeSDK = $this->createMock(StripeSDK::class);

        // 10.505 should round to 1051 cents (PHP's round() rounds .5 up)
        $mockStripeSDK->expects($this->once())
            ->method('makeCharge')
            ->with($this->equalTo(1051))
            ->willReturn(1051);

        $adapter = new StripeAdapter($mockStripeSDK);
        $adapter->processPayment(10.505);
    }

    /**
     * Test that StripeAdapter returns false when charge fails.
     */
    public function test_stripe_adapter_returns_false_on_failure(): void
    {
        $mockStripeSDK = $this->createMock(StripeSDK::class);

        // Simulate a failed charge (returns 0)
        $mockStripeSDK->method('makeCharge')->willReturn(0);

        $adapter = new StripeAdapter($mockStripeSDK);
        $result = $adapter->processPayment(10.50);

        $this->assertFalse($result);
    }

    /**
     * Test that StripeAdapter accepts StripeSDK in constructor.
     */
    public function test_stripe_adapter_accepts_stripe_sdk_in_constructor(): void
    {
        $stripeSDK = new StripeSDK();
        $adapter = new StripeAdapter($stripeSDK);

        $this->assertInstanceOf(StripeAdapter::class, $adapter);
    }
}
