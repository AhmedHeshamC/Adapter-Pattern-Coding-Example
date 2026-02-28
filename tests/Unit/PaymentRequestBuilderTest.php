<?php

namespace Tests\Unit;

use PHPUnit\Framework\TestCase;
use App\Payment\Builder\PaymentRequestBuilder;
use App\Payment\DTO\PaymentRequest;
use InvalidArgumentException;

class PaymentRequestBuilderTest extends TestCase
{
    /**
     * Test that builder can be instantiated.
     */
    public function test_builder_can_be_instantiated(): void
    {
        $builder = new PaymentRequestBuilder();

        $this->assertInstanceOf(PaymentRequestBuilder::class, $builder);
    }

    /**
     * Test that builder creates a PaymentRequest object.
     */
    public function test_builder_creates_payment_request(): void
    {
        $request = (new PaymentRequestBuilder())->build();

        $this->assertInstanceOf(PaymentRequest::class, $request);
    }

    /**
     * Test that builder has default values.
     */
    public function test_builder_has_default_values(): void
    {
        $request = (new PaymentRequestBuilder())->build();

        $this->assertEquals(0.0, $request->getAmount());
        $this->assertEquals('USD', $request->getCurrency());
    }

    /**
     * Test setAmount method returns builder for chaining.
     */
    public function test_set_amount_returns_builder(): void
    {
        $builder = new PaymentRequestBuilder();

        $result = $builder->setAmount(10.50);

        $this->assertSame($builder, $result);
    }

    /**
     * Test setCurrency method returns builder for chaining.
     */
    public function test_set_currency_returns_builder(): void
    {
        $builder = new PaymentRequestBuilder();

        $result = $builder->setCurrency('EUR');

        $this->assertSame($builder, $result);
    }

    /**
     * Test fluent interface for building payment request.
     */
    public function test_fluent_interface(): void
    {
        $request = (new PaymentRequestBuilder())
            ->setAmount(99.99)
            ->setCurrency('EUR')
            ->build();

        $this->assertEquals(99.99, $request->getAmount());
        $this->assertEquals('EUR', $request->getCurrency());
    }

    /**
     * Test builder with various amounts.
     *
     * @dataProvider amountProvider
     */
    public function test_builder_with_various_amounts(float $amount): void
    {
        $request = (new PaymentRequestBuilder())
            ->setAmount($amount)
            ->build();

        $this->assertEquals($amount, $request->getAmount());
    }

    /**
     * Data provider for amount tests.
     */
    public static function amountProvider(): array
    {
        return [
            'zero' => [0.0],
            'small amount' => [0.01],
            'regular amount' => [10.50],
            'large amount' => [9999.99],
        ];
    }

    /**
     * Test builder with various currencies.
     *
     * @dataProvider currencyProvider
     */
    public function test_builder_with_various_currencies(string $currency): void
    {
        $request = (new PaymentRequestBuilder())
            ->setCurrency($currency)
            ->build();

        $this->assertEquals($currency, $request->getCurrency());
    }

    /**
     * Data provider for currency tests.
     */
    public static function currencyProvider(): array
    {
        return [
            'USD' => ['USD'],
            'EUR' => ['EUR'],
            'GBP' => ['GBP'],
            'JPY' => ['JPY'],
        ];
    }

    /**
     * Test setDescription method.
     */
    public function test_set_description(): void
    {
        $request = (new PaymentRequestBuilder())
            ->setDescription('Test payment')
            ->build();

        $this->assertEquals('Test payment', $request->getDescription());
    }

    /**
     * Test setCustomerId method.
     */
    public function test_set_customer_id(): void
    {
        $request = (new PaymentRequestBuilder())
            ->setCustomerId('cust_12345')
            ->build();

        $this->assertEquals('cust_12345', $request->getCustomerId());
    }

    /**
     * Test setMetadata method.
     */
    public function test_set_metadata(): void
    {
        $metadata = ['order_id' => '123', 'product' => 'Premium Plan'];

        $request = (new PaymentRequestBuilder())
            ->setMetadata($metadata)
            ->build();

        $this->assertEquals($metadata, $request->getMetadata());
    }

    /**
     * Test complete builder with all fields.
     */
    public function test_complete_builder_with_all_fields(): void
    {
        $request = (new PaymentRequestBuilder())
            ->setAmount(150.00)
            ->setCurrency('GBP')
            ->setDescription('Premium subscription')
            ->setCustomerId('cust_67890')
            ->setMetadata(['plan' => 'premium', 'billing' => 'monthly'])
            ->build();

        $this->assertEquals(150.00, $request->getAmount());
        $this->assertEquals('GBP', $request->getCurrency());
        $this->assertEquals('Premium subscription', $request->getDescription());
        $this->assertEquals('cust_67890', $request->getCustomerId());
        $this->assertEquals(['plan' => 'premium', 'billing' => 'monthly'], $request->getMetadata());
    }

    /**
     * Test builder validates negative amount.
     */
    public function test_builder_validates_negative_amount(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Amount cannot be negative');

        (new PaymentRequestBuilder())
            ->setAmount(-10.00)
            ->build();
    }

    /**
     * Test builder validates empty currency.
     */
    public function test_builder_validates_empty_currency(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Currency cannot be empty');

        (new PaymentRequestBuilder())
            ->setCurrency('')
            ->build();
    }

    /**
     * Test builder validates currency format (3 letters).
     */
    public function test_builder_validates_currency_format(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Currency must be a valid 3-letter ISO code');

        (new PaymentRequestBuilder())
            ->setCurrency('US')
            ->build();
    }

    /**
     * Test builder can be reused after build.
     */
    public function test_builder_can_be_reused(): void
    {
        $builder = new PaymentRequestBuilder();

        $request1 = $builder->setAmount(10.00)->setCurrency('USD')->build();

        // Modify and build again
        $request2 = $builder->setAmount(20.00)->build();

        // Note: This tests mutable builder behavior
        $this->assertEquals(20.00, $request2->getAmount());
        $this->assertEquals('USD', $request2->getCurrency()); // Currency preserved
    }

    /**
     * Test reset method clears all values.
     */
    public function test_reset_method_clears_all_values(): void
    {
        $builder = new PaymentRequestBuilder();

        $builder->setAmount(100.00)
            ->setCurrency('EUR')
            ->setDescription('Test')
            ->setCustomerId('cust_123')
            ->setMetadata(['key' => 'value']);

        $builder->reset();

        $request = $builder->build();

        $this->assertEquals(0.0, $request->getAmount());
        $this->assertEquals('USD', $request->getCurrency());
        $this->assertNull($request->getDescription());
        $this->assertNull($request->getCustomerId());
        $this->assertNull($request->getMetadata());
    }
}
