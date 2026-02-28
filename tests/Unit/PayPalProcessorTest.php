<?php

namespace Tests\Unit;

use PHPUnit\Framework\TestCase;
use App\Payment\Contracts\PaymentProcessor;
use App\Payment\Processors\PayPalProcessor;

class PayPalProcessorTest extends TestCase
{
    /**
     * Test that PayPalProcessor implements PaymentProcessor interface.
     */
    public function test_paypal_processor_implements_payment_processor(): void
    {
        $processor = new PayPalProcessor();

        $this->assertInstanceOf(PaymentProcessor::class, $processor);
    }

    /**
     * Test that PayPalProcessor can process a payment.
     */
    public function test_paypal_processor_can_process_payment(): void
    {
        $processor = new PayPalProcessor();
        $result = $processor->processPayment(10.50);

        $this->assertTrue($result);
    }

    /**
     * Test that PayPalProcessor accepts amounts in dollars (no conversion needed).
     *
     * @dataProvider paymentAmountProvider
     */
    public function test_paypal_processor_accepts_dollar_amounts(float $amount, bool $expectedSuccess): void
    {
        $processor = new PayPalProcessor();
        $result = $processor->processPayment($amount);

        $this->assertEquals($expectedSuccess, $result);
    }

    /**
     * Data provider for payment amount tests.
     */
    public static function paymentAmountProvider(): array
    {
        return [
            'positive amount' => [10.50, true],
            'large amount' => [1000.00, true],
            'small amount' => [0.01, true],
            'zero amount fails' => [0.00, false],
            'negative amount fails' => [-10.00, false],
        ];
    }

    /**
     * Test that PayPalProcessor can be instantiated.
     */
    public function test_paypal_processor_can_be_instantiated(): void
    {
        $processor = new PayPalProcessor();

        $this->assertInstanceOf(PayPalProcessor::class, $processor);
    }
}
