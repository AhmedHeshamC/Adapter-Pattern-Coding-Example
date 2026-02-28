<?php

namespace Tests\Unit;

use PHPUnit\Framework\TestCase;
use App\Payment\DTO\PaymentRequest;

class PaymentRequestTest extends TestCase
{
    /**
     * Test PaymentRequest can be instantiated.
     */
    public function test_payment_request_can_be_instantiated(): void
    {
        $request = new PaymentRequest(
            amount: 10.50,
            currency: 'USD'
        );

        $this->assertInstanceOf(PaymentRequest::class, $request);
    }

    /**
     * Test PaymentRequest stores amount correctly.
     */
    public function test_payment_request_stores_amount(): void
    {
        $request = new PaymentRequest(amount: 99.99, currency: 'USD');

        $this->assertEquals(99.99, $request->getAmount());
    }

    /**
     * Test PaymentRequest stores currency correctly.
     */
    public function test_payment_request_stores_currency(): void
    {
        $request = new PaymentRequest(amount: 10.00, currency: 'EUR');

        $this->assertEquals('EUR', $request->getCurrency());
    }

    /**
     * Test PaymentRequest with all optional fields.
     */
    public function test_payment_request_with_optional_fields(): void
    {
        $request = new PaymentRequest(
            amount: 150.00,
            currency: 'GBP',
            description: 'Premium subscription',
            customerId: 'cust_12345',
            metadata: ['plan' => 'premium']
        );

        $this->assertEquals(150.00, $request->getAmount());
        $this->assertEquals('GBP', $request->getCurrency());
        $this->assertEquals('Premium subscription', $request->getDescription());
        $this->assertEquals('cust_12345', $request->getCustomerId());
        $this->assertEquals(['plan' => 'premium'], $request->getMetadata());
    }

    /**
     * Test PaymentRequest optional fields default to null.
     */
    public function test_optional_fields_default_to_null(): void
    {
        $request = new PaymentRequest(amount: 10.00, currency: 'USD');

        $this->assertNull($request->getDescription());
        $this->assertNull($request->getCustomerId());
        $this->assertNull($request->getMetadata());
    }

    /**
     * Test PaymentRequest toArray method.
     */
    public function test_payment_request_to_array(): void
    {
        $request = new PaymentRequest(
            amount: 50.00,
            currency: 'USD',
            description: 'Test payment',
            customerId: 'cust_999',
            metadata: ['ref' => 'order_123']
        );

        $array = $request->toArray();

        $this->assertIsArray($array);
        $this->assertEquals(50.00, $array['amount']);
        $this->assertEquals('USD', $array['currency']);
        $this->assertEquals('Test payment', $array['description']);
        $this->assertEquals('cust_999', $array['customerId']);
        $this->assertEquals(['ref' => 'order_123'], $array['metadata']);
    }
}
