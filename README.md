# Multi-Gateway Payment System

A Laravel-based payment processing system demonstrating the **Adapter**, **Factory**, and **Builder** design patterns. This system provides a unified interface for processing payments through multiple payment gateways (Stripe, PayPal) while abstracting away their internal differences.

## Table of Contents

- [Overview](#overview)
- [Architecture](#architecture)
- [Design Patterns](#design-patterns)
  - [Adapter Pattern](#adapter-pattern)
  - [Factory Pattern](#factory-pattern)
  - [Builder Pattern](#builder-pattern)
- [Installation](#installation)
- [Usage](#usage)
- [API Reference](#api-reference)
- [Testing](#testing)
- [Project Structure](#project-structure)
- [Extending the System](#extending-the-system)

## Overview

This project solves a common problem in payment processing: integrating multiple payment gateways with incompatible interfaces. The Stripe SDK expects amounts in cents, while our application works with dollars. The solution uses design patterns to create a flexible, maintainable system.

### Key Features

- **Unified Interface**: Process payments through any gateway using the same API
- **Gateway Abstraction**: Client code doesn't need to know which gateway is being used
- **Easy Extension**: Add new payment gateways without modifying existing code
- **Input Validation**: Payment requests are validated before processing
- **Comprehensive Testing**: 78 unit tests with 124 assertions

## Architecture

```
┌─────────────────────────────────────────────────────────────────┐
│                         Client Code                              │
│                                                                 │
│  $processor = PaymentGatewayFactory::getProcessor('stripe');   │
│  $processor->processPayment(10.50);                             │
│                                                                 │
└─────────────────────────────────────────────────────────────────┘
                                │
                                ▼
┌─────────────────────────────────────────────────────────────────┐
│                  PaymentProcessor Interface                      │
│                                                                 │
│              processPayment(float $amount): bool                 │
│                                                                 │
└─────────────────────────────────────────────────────────────────┘
                    │                               │
                    ▼                               ▼
┌──────────────────────────────┐   ┌──────────────────────────────┐
│       StripeAdapter          │   │      PayPalProcessor         │
│       (Adapter Pattern)      │   │      (Native Implementation) │
│                              │   │                              │
│  - Converts dollars to cents │   │  - Uses dollars directly     │
│  - Wraps StripeSDK           │   │  - No conversion needed      │
└──────────────────────────────┘   └──────────────────────────────┘
                │
                ▼
┌──────────────────────────────┐
│         StripeSDK            │
│       (Third-party Mock)     │
│                              │
│  makeCharge(int $cents)      │
└──────────────────────────────┘


┌─────────────────────────────────────────────────────────────────┐
│                  PaymentRequestBuilder                           │
│                                                                  │
│  $request = (new PaymentRequestBuilder())                       │
│      ->setAmount(99.99)                                         │
│      ->setCurrency('USD')                                       │
│      ->setDescription('Order #123')                             │
│      ->build();                                                 │
│                                                                  │
└─────────────────────────────────────────────────────────────────┘
                                │
                                ▼
┌─────────────────────────────────────────────────────────────────┐
│                     PaymentRequest (DTO)                         │
│                                                                  │
│  Immutable object containing validated payment data              │
│                                                                  │
└─────────────────────────────────────────────────────────────────┘
```

## Design Patterns

### Adapter Pattern

The Adapter pattern allows incompatible interfaces to work together. In our case, Stripe's SDK expects amounts in cents, but our application uses dollars.

**Problem**: Stripe SDK's `makeCharge()` expects cents, our interface uses dollars.

**Solution**: `StripeAdapter` converts the amount and delegates to `StripeSDK`.

```php
// Without Adapter (doesn't work)
$stripeSDK = new StripeSDK();
$stripeSDK->makeCharge(10.50);  // Wrong! Expects 1050 cents

// With Adapter
$adapter = new StripeAdapter(new StripeSDK());
$adapter->processPayment(10.50);  // Correct! Adapter converts to 1050 cents
```

**Key Components**:

| Component | Role | Class |
|-----------|------|-------|
| Target | The interface our app uses | `PaymentProcessor` |
| Adapter | Translates between interfaces | `StripeAdapter` |
| Adaptee | The incompatible third-party code | `StripeSDK` |

### Factory Pattern

The Factory pattern provides a centralized way to create payment processors, allowing client code to be agnostic about which gateway is being used.

**Problem**: Client code shouldn't need to know how to instantiate different processors.

**Solution**: `PaymentGatewayFactory` creates the correct processor based on a configuration string.

```php
// Without Factory (client knows too much)
if ($provider === 'stripe') {
    $processor = new StripeAdapter(new StripeSDK());
} elseif ($provider === 'paypal') {
    $processor = new PayPalProcessor();
}

// With Factory (client is agnostic)
$processor = PaymentGatewayFactory::getProcessor($provider);
$processor->processPayment(10.50);
```

**Benefits**:

- Single point of configuration
- Easy to add new providers
- Client code is decoupled from concrete implementations

### Builder Pattern

The Builder pattern constructs complex objects step-by-step with validation.

**Problem**: Payment requests have many optional fields and need validation.

**Solution**: `PaymentRequestBuilder` provides a fluent interface with built-in validation.

```php
// Without Builder (unwieldy constructor)
$request = new PaymentRequest(99.99, 'USD', null, 'cust_123', null);

// With Builder (readable and validated)
$request = (new PaymentRequestBuilder())
    ->setAmount(99.99)
    ->setCurrency('USD')
    ->setCustomerId('cust_123')
    ->setDescription('Premium subscription')
    ->setMetadata(['plan' => 'premium'])
    ->build();  // Validates and creates PaymentRequest
```

**Validation**:

- Amount must be non-negative
- Currency must be a valid 3-letter ISO code (e.g., USD, EUR)

## Installation

### Requirements

- PHP 8.1+
- Composer
- Laravel 10.x

### Setup

```bash
# Clone the repository
git clone https://github.com/AhmedHeshamC/Adapter-Pattern-Coding-Example.git
cd Adapter-Pattern-Coding-Example

# Install dependencies
composer install

# Copy environment file
cp .env.example .env

# Generate application key
php artisan key:generate
```

## Usage

### Basic Payment Processing

```php
use App\Payment\Factory\PaymentGatewayFactory;

// Get a processor for Stripe
$processor = PaymentGatewayFactory::getProcessor('stripe');

// Process a payment
$success = $processor->processPayment(10.50);

if ($success) {
    echo "Payment processed successfully!";
}
```

### Using Different Gateways

```php
use App\Payment\Factory\PaymentGatewayFactory;

// Stripe
$stripe = PaymentGatewayFactory::getProcessor('stripe');
$stripe->processPayment(10.50);

// PayPal
$paypal = PaymentGatewayFactory::getProcessor('paypal');
$paypal->processPayment(25.00);

// Case-insensitive
$processor = PaymentGatewayFactory::getProcessor('STRIPE');  // Works
$processor = PaymentGatewayFactory::getProcessor('Stripe');  // Works
```

### Building Payment Requests

```php
use App\Payment\Builder\PaymentRequestBuilder;
use App\Payment\Factory\PaymentGatewayFactory;

// Build a complete payment request
$request = (new PaymentRequestBuilder())
    ->setAmount(150.00)
    ->setCurrency('EUR')
    ->setDescription('Order #12345')
    ->setCustomerId('cust_67890')
    ->setMetadata([
        'order_id' => '12345',
        'items' => ['Product A', 'Product B']
    ])
    ->build();

// Process using the request data
$processor = PaymentGatewayFactory::getProcessor('stripe');
$success = $processor->processPayment($request->getAmount());
```

### Checking Supported Providers

```php
use App\Payment\Factory\PaymentGatewayFactory;

// Get all supported providers
$providers = PaymentGatewayFactory::getSupportedProviders();
// Returns: ['STRIPE', 'PAYPAL']

// Check if a provider is supported
if (PaymentGatewayFactory::isProviderSupported('stripe')) {
    $processor = PaymentGatewayFactory::getProcessor('stripe');
}
```

### Handling Errors

```php
use App\Payment\Factory\PaymentGatewayFactory;
use InvalidArgumentException;

try {
    $processor = PaymentGatewayFactory::getProcessor('unknown');
} catch (InvalidArgumentException $e) {
    echo $e->getMessage();
    // "Payment provider 'UNKNOWN' is not supported. Supported providers: STRIPE, PAYPAL"
}
```

## API Reference

### PaymentProcessor Interface

```php
interface PaymentProcessor
{
    public function processPayment(float $amount): bool;
}
```

### PaymentGatewayFactory

| Method | Parameters | Return | Description |
|--------|------------|--------|-------------|
| `getProcessor(string $provider)` | Provider name (e.g., 'stripe', 'paypal') | `PaymentProcessor` | Returns the appropriate processor |
| `getSupportedProviders()` | None | `array<string>` | Returns list of supported providers |
| `isProviderSupported(string $provider)` | Provider name | `bool` | Checks if provider is supported |

### PaymentRequestBuilder

| Method | Parameters | Return | Description |
|--------|------------|--------|-------------|
| `setAmount(float $amount)` | Amount in dollars | `self` | Sets the payment amount |
| `setCurrency(string $currency)` | 3-letter ISO code | `self` | Sets the currency |
| `setDescription(string $description)` | Description text | `self` | Sets payment description |
| `setCustomerId(string $id)` | Customer identifier | `self` | Sets customer ID |
| `setMetadata(array $data)` | Key-value pairs | `self` | Sets additional metadata |
| `build()` | None | `PaymentRequest` | Validates and creates the request |
| `reset()` | None | `self` | Resets builder to defaults |

### PaymentRequest DTO

| Method | Return | Description |
|--------|--------|-------------|
| `getAmount()` | `float` | Returns the payment amount |
| `getCurrency()` | `string` | Returns the currency code |
| `getDescription()` | `?string` | Returns the description or null |
| `getCustomerId()` | `?string` | Returns the customer ID or null |
| `getMetadata()` | `?array` | Returns the metadata or null |
| `toArray()` | `array` | Converts to associative array |

## Testing

### Run All Tests

```bash
php artisan test
```

### Run Specific Test Suites

```bash
# Test Stripe Adapter
php artisan test --filter=StripeAdapter

# Test Factory
php artisan test --filter=PaymentGatewayFactory

# Test Builder
php artisan test --filter=PaymentRequestBuilder
```

### Test Coverage

| Component | Tests | Assertions |
|-----------|-------|------------|
| StripeSDK | 5 | 5 |
| StripeAdapter | 14 | 14 |
| PayPalProcessor | 8 | 8 |
| PaymentGatewayFactory | 17 | 26 |
| PaymentRequest | 6 | 11 |
| PaymentRequestBuilder | 23 | 36 |
| **Total** | **78** | **124** |

### Key Test Cases

- **Dollar to Cents Conversion**: Verifies `$10.50` converts to `1050` cents
- **Case Insensitivity**: Factory accepts 'stripe', 'STRIPE', 'Stripe'
- **Validation**: Builder rejects negative amounts and invalid currencies
- **Interface Compliance**: All processors implement `PaymentProcessor`

## Project Structure

```
app/
└── Payment/
    ├── Contracts/
    │   └── PaymentProcessor.php      # Target interface
    ├── Adaptee/
    │   └── StripeSDK.php             # Third-party SDK mock
    ├── Adapter/
    │   └── StripeAdapter.php         # Adapter implementation
    ├── Processors/
    │   └── PayPalProcessor.php       # Native processor
    ├── Factory/
    │   └── PaymentGatewayFactory.php # Factory for creating processors
    ├── Builder/
    │   └── PaymentRequestBuilder.php # Builder for payment requests
    └── DTO/
        └── PaymentRequest.php        # Immutable payment data

tests/
└── Unit/
    ├── PaymentProcessorTest.php
    ├── StripeSDKTest.php
    ├── StripeAdapterTest.php
    ├── PayPalProcessorTest.php
    ├── PaymentGatewayFactoryTest.php
    ├── PaymentRequestBuilderTest.php
    └── PaymentRequestTest.php
```

## Extending the System

### Adding a New Payment Gateway

1. **Create the Processor** (if it implements our interface natively):

```php
// app/Payment/Processors/SquareProcessor.php
namespace App\Payment\Processors;

use App\Payment\Contracts\PaymentProcessor;

class SquareProcessor implements PaymentProcessor
{
    public function processPayment(float $amount): bool
    {
        // Square API implementation
        return $amount > 0;
    }
}
```

2. **Or Create an Adapter** (if it has an incompatible interface):

```php
// app/Payment/Adapter/SquareAdapter.php
namespace App\Payment\Adapter;

use App\Payment\Contracts\PaymentProcessor;
use App\Payment\Adaptee\SquareSDK;

class SquareAdapter implements PaymentProcessor
{
    public function __construct(private SquareSDK $sdk) {}

    public function processPayment(float $amount): bool
    {
        // Transform data and delegate
        return $this->sdk->charge($amount);
    }
}
```

3. **Register in Factory**:

```php
// app/Payment/Factory/PaymentGatewayFactory.php

public const PROVIDER_SQUARE = 'SQUARE';

public static function getProcessor(string $provider): PaymentProcessor
{
    return match (strtoupper($provider)) {
        // ... existing cases
        self::PROVIDER_SQUARE => self::createSquareProcessor(),
        default => throw new InvalidArgumentException(...)
    };
}

private static function createSquareProcessor(): PaymentProcessor
{
    return new SquareProcessor();
    // Or: return new SquareAdapter(new SquareSDK());
}
```

4. **Write Tests**:

```php
// tests/Unit/SquareProcessorTest.php
class SquareProcessorTest extends TestCase
{
    public function test_square_processor_implements_payment_processor(): void
    {
        $processor = new SquareProcessor();
        $this->assertInstanceOf(PaymentProcessor::class, $processor);
    }
    // ... more tests
}
```

## License

This project is open-sourced software licensed under the [MIT license](https://opensource.org/licenses/MIT).

## Contributing

1. Fork the repository
2. Create a feature branch (`git checkout -b feature/amazing-feature`)
3. Commit your changes (`git commit -m 'Add amazing feature'`)
4. Push to the branch (`git push origin feature/amazing-feature`)
5. Open a Pull Request
