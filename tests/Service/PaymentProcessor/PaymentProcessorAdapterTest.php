<?php

namespace Service\PaymentProcessor;

use App\Service\PaymentProcessor\PaypalProcessorAdapter;
use App\Service\PaymentProcessor\StripeProcessorAdapter;
use PHPUnit\Framework\TestCase;
use Systemeio\TestForCandidates\PaymentProcessor\PaypalPaymentProcessor;
use Systemeio\TestForCandidates\PaymentProcessor\StripePaymentProcessor;

class PaymentProcessorAdapterTest extends TestCase
{
    private PaypalProcessorAdapter $paypalProcessorAdapter;
    private StripeProcessorAdapter $stripeProcessorAdapter;

    public function testPaypalProcessorSuccess(): void
    {
        $this->assertTrue($this->paypalProcessorAdapter->process(999.09)); // 99909 центов - должно пройти
    }

    public function testPaypalProcessorFailure(): void
    {
        $this->assertFalse($this->paypalProcessorAdapter->process(1000.01)); // 100001 цент - должно провалиться
    }

    public function testStripeProcessorSuccess(): void
    {
        $this->assertTrue($this->stripeProcessorAdapter->process(100.01)); // > 100 - должно пройти
    }

    public function testStripeProcessorFailure(): void
    {
        $this->assertFalse($this->stripeProcessorAdapter->process(90.09)); // < 100 - должно провалиться
    }

    protected function setUp(): void
    {
        $this->paypalProcessorAdapter = new PaypalProcessorAdapter(new PaypalPaymentProcessor());
        $this->stripeProcessorAdapter = new StripeProcessorAdapter(new StripePaymentProcessor());
    }
} 