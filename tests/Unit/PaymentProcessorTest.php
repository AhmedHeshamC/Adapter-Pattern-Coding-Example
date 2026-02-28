<?php

namespace Tests\Unit;

use PHPUnit\Framework\TestCase;
use App\Payment\Contracts\PaymentProcessor;

class PaymentProcessorTest extends TestCase
{
    /**
     * Test that PaymentProcessor interface exists.
     */
    public function test_payment_processor_interface_exists(): void
    {
        $this->assertTrue(interface_exists(PaymentProcessor::class));
    }

    /**
     * Test that PaymentProcessor interface has processPayment method.
     */
    public function test_payment_processor_has_process_payment_method(): void
    {
        $reflection = new \ReflectionClass(PaymentProcessor::class);

        $this->assertTrue($reflection->hasMethod('processPayment'));

        $method = $reflection->getMethod('processPayment');
        $this->assertEquals('processPayment', $method->getName());
    }

    /**
     * Test that processPayment method accepts float parameter.
     */
    public function test_process_payment_accepts_float_parameter(): void
    {
        $reflection = new \ReflectionClass(PaymentProcessor::class);
        $method = $reflection->getMethod('processPayment');
        $parameters = $method->getParameters();

        $this->assertCount(1, $parameters);
        $this->assertEquals('amount', $parameters[0]->getName());
    }
}
