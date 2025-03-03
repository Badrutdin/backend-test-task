<?php

namespace Service;

use App\Service\TaxCalculator;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

class TaxCalculatorTest extends TestCase
{
    private TaxCalculator $taxCalculator;

    // Выполняется перед каждым тестом
    protected function setUp(): void
    {
        $this->taxCalculator = new TaxCalculator();
    }

    // Тест с данными из провайдера
    #[DataProvider('taxRateProvider')]
    public function testCalculateTax(string $taxNumber, float $price, float $expectedTax): void
    {
        $tax = $this->taxCalculator->calculateTax($price, $taxNumber);
        $this->assertEquals($expectedTax, $tax);
    }

    // Тест на неверный код страны
    public function testInvalidCountryCode(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->taxCalculator->calculateTax(100, 'US123456789');
    }

    // Провайдер тестовых данных
    public static function taxRateProvider(): array
    {
        return [
            'German tax 19%' => ['DE123456789', 100.0, 19.0],    // Проверяем немецкий НДС
            'Italian tax 22%' => ['IT12345678900', 100.0, 22.0], // Проверяем итальянский НДС
            'French tax 20%' => ['FRAA123456789', 100.0, 20.0],  // Проверяем французский НДС
            'Greek tax 24%' => ['GR123456789', 100.0, 24.0],     // Проверяем греческий НДС
            'Zero price' => ['DE123456789', 0.0, 0.0],           // Проверяем нулевую цену
        ];
    }
} 