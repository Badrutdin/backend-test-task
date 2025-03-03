<?php

namespace Service\PaymentProcessor;

use App\Service\PaymentProcessor\PaymentProcessorFactory;
use App\Service\PaymentProcessor\PaypalProcessorAdapter;
use App\Service\PaymentProcessor\StripeProcessorAdapter;
use PHPUnit\Framework\TestCase;

class PaymentProcessorFactoryTest extends TestCase
{
    private PaymentProcessorFactory $factory;

    protected function setUp(): void
    {
        $this->factory = new PaymentProcessorFactory();
    }

    public function testCreatePaypalProcessor(): void
    {
        $processor = $this->factory->create('paypal');
        $this->assertInstanceOf(PaypalProcessorAdapter::class, $processor);
        
        // Проверяем, что процессор работает корректно
        $this->assertTrue($processor->process(50.0)); // 5000 центов - должно пройти
        $this->assertFalse($processor->process(1500.0)); // 150000 центов - должно провалиться
    }

    public function testCreateStripeProcessor(): void
    {
        $processor = $this->factory->create('stripe');
        $this->assertInstanceOf(StripeProcessorAdapter::class, $processor);
        
        // Проверяем логику Stripe
        $this->assertFalse($processor->process(50.0)); // < 100 должно вернуть false
        $this->assertTrue($processor->process(150.0)); // > 100 должно вернуть true
    }

    public function testInvalidProcessorType(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->factory->create('invalid');
    }
} 