<?php

namespace Service\PaymentProcessor;

use App\Service\PaymentProcessor\PaymentProcessorFactory;
use App\Service\PaymentProcessor\PaymentProcessorInterface;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

class PaymentProcessorFactoryTest extends TestCase
{
    public static function invalidProcessorTypesProvider(): array
    {
        $paypalProcessor = (new self('PaymentProcessorFactoryTest'))->createMock(PaymentProcessorInterface::class);
        $stripeProcessor = (new self('PaymentProcessorFactoryTest'))->createMock(PaymentProcessorInterface::class);
        return [
            'Invalid type with populated factory' => [
                ['paypal' => $paypalProcessor, 'stripe' => $stripeProcessor],
                'invalid_type'
            ],
            'Invalid type with empty factory' => [
                [],
                'invalid_type'
            ],
            'Valid type with empty factory' => [
                [],
                'stripe'
            ],
            'Valid type with incomplete factory' => [
                [],
                'stripe'
            ],
        ];
    }

    public function testCreateReturnsCorrectProcessor()
    {
        $paypalProcessor = $this->createMock(PaymentProcessorInterface::class);
        $stripeProcessor = $this->createMock(PaymentProcessorInterface::class);

        $factory = $this->getFactory([
            'paypal' => $paypalProcessor,
            'stripe' => $stripeProcessor,
        ]);

        $this->assertSame($paypalProcessor, $factory->create('paypal'));
        $this->assertSame($stripeProcessor, $factory->create('stripe'));
    }

    private function getFactory(array $processors = []): PaymentProcessorFactory
    {
        return new PaymentProcessorFactory($processors);
    }

    public function testCreateThrowsExceptionForInvalidTypeWithProcessors()
    {
        $paypalProcessor = $this->createMock(PaymentProcessorInterface::class);
        $factory = $this->getFactory(['paypal' => $paypalProcessor]);

        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage("Invalid payment processor type: invalid_type");

        $factory->create('invalid_type');
    }

    #[DataProvider('invalidProcessorTypesProvider')]
    public function testCreateThrowsExceptionForInvalidType(array $processors, string $invalidType)
    {
        $factory = $this->getFactory($processors);

        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage("Invalid payment processor type: $invalidType");

        $factory->create($invalidType);
    }
}
