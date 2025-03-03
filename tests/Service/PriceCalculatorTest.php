<?php

namespace Service;

use App\Entity\Coupon;
use App\Entity\Product;
use App\Service\PriceCalculator;
use App\Service\TaxCalculator;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\EntityRepository;
use PHPUnit\Framework\TestCase;

class PriceCalculatorTest extends TestCase
{
    private EntityManagerInterface $entityManager;
    private TaxCalculator $taxCalculator;
    private PriceCalculator $priceCalculator;

    protected function setUp(): void
    {
        // Создаем моки для зависимостей
        $this->entityManager = $this->createMock(EntityManagerInterface::class);
        $this->taxCalculator = new TaxCalculator();
        $this->priceCalculator = new PriceCalculator(
            $this->entityManager,
            $this->taxCalculator
        );
    }

    public function testCalculateWithPercentageCoupon(): void
    {
        // Подготавливаем тестовые данные
        $product = new Product();
        $product->setPrice(100);

        $coupon = new Coupon();
        $coupon->setType('percentage');
        $coupon->setValue(15); // 15% скидка

        // Настраиваем моки репозиториев
        $productRepo = $this->createMock(EntityRepository::class);
        $productRepo->method('find')->willReturn($product);

        $couponRepo = $this->createMock(EntityRepository::class);
        $couponRepo->method('findOneBy')->willReturn($coupon);

        // Настраиваем EntityManager
        $this->entityManager
            ->method('getRepository')
            ->willReturnMap([
                [Product::class, $productRepo],
                [Coupon::class, $couponRepo],
            ]);

        // Проверяем расчет
        // 100 EUR - 15% = 85 EUR + 19% налог = 101.15 EUR
        $price = $this->priceCalculator->calculate(1, 'DE123456789', 'D15');
        $this->assertEquals(101.15, $price);
    }

    public function testProductNotFound(): void
    {
        // Настраиваем мок для отсутствующего продукта
        $productRepo = $this->createMock(EntityRepository::class);
        $productRepo->method('find')->willReturn(null);

        $this->entityManager
            ->method('getRepository')
            ->willReturn($productRepo);

        // Проверяем выброс исключения
        $this->expectException(\InvalidArgumentException::class);
        $this->priceCalculator->calculate(999, 'DE123456789');
    }
} 