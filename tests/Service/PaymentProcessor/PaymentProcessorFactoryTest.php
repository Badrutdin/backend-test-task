<?php

namespace Service\PaymentProcessor;

use App\Service\PaymentProcessor\PaymentProcessorFactory;
use App\Service\PaymentProcessor\PaypalProcessorAdapter;
use App\Service\PaymentProcessor\StripeProcessorAdapter;
use PHPUnit\Framework\TestCase;

class PaymentProcessorFactoryTest extends TestCase
{
    private PaymentProcessorFactory $factory;

    public function testCreatePaypalProcessor(): void
    {
        $processor = $this->factory->create('paypal');
        $this->assertInstanceOf(PaypalProcessorAdapter::class, $processor);

    }

    public function testCreateStripeProcessor(): void
    {
        $processor = $this->factory->create('stripe');
        $this->assertInstanceOf(StripeProcessorAdapter::class, $processor);
    }

    public function testInvalidProcessorType(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->factory->create('invalid');
    }

    protected function setUp(): void
    {
        $this->factory = new PaymentProcessorFactory();
    }
} 