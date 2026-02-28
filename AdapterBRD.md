# BRD: Multi-Gateway Payment System (Stripe Integration)

## 1. Project Overview

The objective is to expand our existing payment processing system to support Stripe. Since the Stripe SDK is incompatible with our internal PaymentProcessor interface, we will implement the **Adapter Design Pattern**. We will also leverage our previous knowledge of Factory and Builder patterns to ensure the system is scalable and easy to configure.

---

## 2. User Stories & Acceptance Criteria

### Story 1: Stripe Adapter Implementation

**As a Developer,**
**I want** to create a wrapper for the Stripe SDK,
**So that** I can process payments through Stripe using our standard `processPayment` method without changing the core application logic.

**Acceptance Criteria:**
- Create a `StripeAdapter` class that implements `PaymentProcessor`.
- The adapter must handle the unit conversion (converting amount in dollars to valueInCents).
- The adapter must map `processPayment()` to Stripe's `makeCharge()`.

---

### Story 2: Dynamic Gateway Selection (Factory)

**As a System Architect,**
**I want** a centralized way to instantiate payment providers,
**So that** the client code doesn't need to know whether it's using PayPal, Stripe, or any future gateway.

**Acceptance Criteria:**
- Implement a `PaymentGatewayFactory`.
- The factory should return the correct `PaymentProcessor` instance based on a configuration string.

---

### Story 3: Complex Payment Configuration (Builder)

**As a Lead Engineer,**
**I want** to use a Builder to construct payment metadata (currency, description, user info),
**So that** the payment request is fully validated before being passed to the Adapter.

**Acceptance Criteria:**
- Implement a `PaymentRequestBuilder` to create a standard `PaymentRequest` object.

---

## 3. Technical Architecture

| Component | Role | Description |
|-----------|------|-------------|
| **Target** | `PaymentProcessor` | The existing interface our app uses. |
| **Adapter** | `StripeAdapter` | The class that translates our interface to Stripe's SDK. |
| **Adaptee** | `StripeSDK` | The 3rd party tool with an incompatible method (`makeCharge`). |
| **Client** | `PaymentService` | The core logic that only talks to the Interface. |

---

## 4. Sprint Task Breakdown

### Task 1: Define the Incompatible SDK (Mock)

Create a simulation of the Stripe SDK to represent the external dependency.

```typescript
class StripeSDK {
    // Incompatible method: uses cents instead of dollars
    makeCharge(valueInCents: number): void {
        console.log(`[Stripe SDK] Successfully charged ${valueInCents} cents.`);
    }
}
```

### Task 2: Implement the Stripe Adapter

Apply the Adapter pattern to bridge the gap.

```typescript
class StripeAdapter implements PaymentProcessor {
    private stripeSDK: StripeSDK;

    constructor(sdk: StripeSDK) {
        this.stripeSDK = sdk;
    }

    processPayment(amount: number): void {
        // Step 1: Data transformation (Dollars -> Cents)
        const amountInCents = Math.round(amount * 100);

        // Step 2: Method mapping
        this.stripeSDK.makeCharge(amountInCents);
    }
}
```

### Task 3: Integrate the Factory Pattern

Use the Factory to hide the complexity of which adapter is being used.

```typescript
class PaymentFactory {
    static getProcessor(provider: string): PaymentProcessor {
        switch(provider.toUpperCase()) {
            case 'STRIPE':
                return new StripeAdapter(new StripeSDK());
            case 'PAYPAL':
                return new PayPalProcessor();
            default:
                throw new Error("Provider not supported.");
        }
    }
}
```

### Task 4: Implement the Builder Pattern (Bonus Context)

Create a PaymentRequest builder to handle complex payment details before processing.

```typescript
class PaymentRequestBuilder {
    private amount: number = 0;
    private currency: string = "USD";

    setAmount(val: number) { this.amount = val; return this; }
    setCurrency(curr: string) { this.currency = curr; return this; }

    build() { return { amount: this.amount, currency: this.currency }; }
}
```

---

## 5. Definition of Done (DoD)

- [ ] All code is written in PHP (Laravel).
- [ ] The `PaymentProcessor` interface remains unmodified.
- [ ] The client code can switch between PayPal and Stripe via the Factory.
- [ ] Unit tests verify that `amount: 10.50` results in `1050` cents for the Stripe Adapter.
