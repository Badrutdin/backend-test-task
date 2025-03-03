<?php

namespace App\Tests\Service\PaymentProcessor;

use App\Service\PaymentProcessor\PaypalProcessorAdapter;
use App\Service\PaymentProcessor\StripeProcessorAdapter;
use PHPUnit\Framework\TestCase;
use Systemeio\TestForCandidates\PaymentProcessor\PaypalPaymentProcessor;
use Systemeio\TestForCandidates\PaymentProcessor\StripePaymentProcessor;

class PaymentProcessorAdapterTest extends TestCase
{
    public function testPaypalProcessorSuccess(): void
    {
        $adapter = new PaypalProcessorAdapter(new PaypalPaymentProcessor());
        $this->assertTrue($adapter->process(50.0)); // 5000 центов - должно пройти
    }

    public function testPaypalProcessorFailure(): void
    {
        $adapter = new PaypalProcessorAdapter(new PaypalPaymentProcessor());
        $this->assertFalse($adapter->process(1500.0)); // 150000 центов - должно провалиться
    }

    public function testStripeProcessorSuccess(): void
    {
        $adapter = new StripeProcessorAdapter(new StripePaymentProcessor());
        $this->assertTrue($adapter->process(150.0)); // > 100 - должно пройти
    }

    public function testStripeProcessorFailure(): void
    {
        $adapter = new StripeProcessorAdapter(new StripePaymentProcessor());
        $this->assertFalse($adapter->process(50.0)); // < 100 - должно провалиться
    }
} 