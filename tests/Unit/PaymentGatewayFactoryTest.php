<?php

namespace Tests\Unit;

use PHPUnit\Framework\TestCase;
use App\Payment\Contracts\PaymentProcessor;
use App\Payment\Factory\PaymentGatewayFactory;
use App\Payment\Adapter\StripeAdapter;
use App\Payment\Processors\PayPalProcessor;
use InvalidArgumentException;

class PaymentGatewayFactoryTest extends TestCase
{
    /**
     * Test that factory returns a PaymentProcessor instance for Stripe.
     */
    public function test_factory_returns_payment_processor_for_stripe(): void
    {
        $processor = PaymentGatewayFactory::getProcessor('stripe');

        $this->assertInstanceOf(PaymentProcessor::class, $processor);
    }

    /**
     * Test that factory returns a PaymentProcessor instance for PayPal.
     */
    public function test_factory_returns_payment_processor_for_paypal(): void
    {
        $processor = PaymentGatewayFactory::getProcessor('paypal');

        $this->assertInstanceOf(PaymentProcessor::class, $processor);
    }

    /**
     * Test that factory returns StripeAdapter for Stripe provider.
     */
    public function test_factory_returns_stripe_adapter_for_stripe(): void
    {
        $processor = PaymentGatewayFactory::getProcessor('stripe');

        $this->assertInstanceOf(StripeAdapter::class, $processor);
    }

    /**
     * Test that factory returns PayPalProcessor for PayPal provider.
     */
    public function test_factory_returns_paypal_processor_for_paypal(): void
    {
        $processor = PaymentGatewayFactory::getProcessor('paypal');

        $this->assertInstanceOf(PayPalProcessor::class, $processor);
    }

    /**
     * Test that factory is case-insensitive.
     *
     * @dataProvider caseInsensitiveProvider
     */
    public function test_factory_is_case_insensitive(string $provider): void
    {
        $processor = PaymentGatewayFactory::getProcessor($provider);

        $this->assertInstanceOf(PaymentProcessor::class, $processor);
    }

    /**
     * Data provider for case insensitivity tests.
     */
    public static function caseInsensitiveProvider(): array
    {
        return [
            'lowercase stripe' => ['stripe'],
            'uppercase STRIPE' => ['STRIPE'],
            'mixed case Stripe' => ['Stripe'],
            'lowercase paypal' => ['paypal'],
            'uppercase PAYPAL' => ['PAYPAL'],
            'mixed case PayPal' => ['PayPal'],
        ];
    }

    /**
     * Test that factory throws exception for unsupported provider.
     */
    public function test_factory_throws_exception_for_unsupported_provider(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage("Payment provider 'SQUARE' is not supported");

        PaymentGatewayFactory::getProcessor('square');
    }

    /**
     * Test that factory exception message lists supported providers.
     */
    public function test_factory_exception_lists_supported_providers(): void
    {
        try {
            PaymentGatewayFactory::getProcessor('unknown');
        } catch (InvalidArgumentException $e) {
            $this->assertStringContainsString('STRIPE', $e->getMessage());
            $this->assertStringContainsString('PAYPAL', $e->getMessage());
        }
    }

    /**
     * Test that Stripe processor works correctly via factory.
     */
    public function test_stripe_processor_works_via_factory(): void
    {
        $processor = PaymentGatewayFactory::getProcessor('stripe');
        $result = $processor->processPayment(10.50);

        $this->assertTrue($result);
    }

    /**
     * Test that PayPal processor works correctly via factory.
     */
    public function test_paypal_processor_works_via_factory(): void
    {
        $processor = PaymentGatewayFactory::getProcessor('paypal');
        $result = $processor->processPayment(25.00);

        $this->assertTrue($result);
    }

    /**
     * Test getSupportedProviders returns array with Stripe and PayPal.
     */
    public function test_get_supported_providers_returns_stripe_and_paypal(): void
    {
        $providers = PaymentGatewayFactory::getSupportedProviders();

        $this->assertIsArray($providers);
        $this->assertContains('STRIPE', $providers);
        $this->assertContains('PAYPAL', $providers);
    }

    /**
     * Test isProviderSupported returns true for supported providers.
     */
    public function test_is_provider_supported_returns_true_for_supported(): void
    {
        $this->assertTrue(PaymentGatewayFactory::isProviderSupported('stripe'));
        $this->assertTrue(PaymentGatewayFactory::isProviderSupported('STRIPE'));
        $this->assertTrue(PaymentGatewayFactory::isProviderSupported('paypal'));
        $this->assertTrue(PaymentGatewayFactory::isProviderSupported('PAYPAL'));
    }

    /**
     * Test isProviderSupported returns false for unsupported providers.
     */
    public function test_is_provider_supported_returns_false_for_unsupported(): void
    {
        $this->assertFalse(PaymentGatewayFactory::isProviderSupported('square'));
        $this->assertFalse(PaymentGatewayFactory::isProviderSupported('venmo'));
        $this->assertFalse(PaymentGatewayFactory::isProviderSupported(''));
    }
}
