<?php

namespace Tests\Unit;

use PHPUnit\Framework\TestCase;
use App\Payment\Adaptee\StripeSDK;

class StripeSDKTest extends TestCase
{
    /**
     * Test that StripeSDK can be instantiated.
     */
    public function test_stripe_sdk_can_be_instantiated(): void
    {
        $stripeSDK = new StripeSDK();

        $this->assertInstanceOf(StripeSDK::class, $stripeSDK);
    }

    /**
     * Test that makeCharge method exists and accepts cents.
     */
    public function test_stripe_sdk_make_charge_accepts_cents(): void
    {
        $stripeSDK = new StripeSDK();

        // This should not throw an exception
        $result = $stripeSDK->makeCharge(1050); // 1050 cents = $10.50

        // Verify the charge was processed (returns the amount)
        $this->assertGreaterThan(0, $result);
    }

    /**
     * Test that makeCharge returns the charged amount in cents.
     */
    public function test_stripe_sdk_make_charge_returns_charged_amount(): void
    {
        $stripeSDK = new StripeSDK();

        $result = $stripeSDK->makeCharge(1050);

        $this->assertEquals(1050, $result);
    }

    /**
     * Test that makeCharge handles zero amount.
     */
    public function test_stripe_sdk_make_charge_handles_zero(): void
    {
        $stripeSDK = new StripeSDK();

        $result = $stripeSDK->makeCharge(0);

        $this->assertEquals(0, $result);
    }

    /**
     * Test that makeCharge handles large amounts.
     */
    public function test_stripe_sdk_make_charge_handles_large_amount(): void
    {
        $stripeSDK = new StripeSDK();

        $result = $stripeSDK->makeCharge(10000000); // 100,000 dollars in cents

        $this->assertEquals(10000000, $result);
    }
}
