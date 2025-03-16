<?php

namespace Service;

use App\Entity\Coupon;
use App\Entity\Product;
use App\Service\PriceCalculator;
use App\Service\TaxCalculator;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\EntityRepository;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

class PriceCalculatorTest extends TestCase
{
    private EntityManagerInterface $entityManager;
    private TaxCalculator $taxCalculator;
    private PriceCalculator $priceCalculator;

    public static function calculateProvider(): array
    {
        return [
            'German tax price 200 discount percentage 20' => ['DE123456789', 20, 'percentage', 190.4, 200],
            'German tax price 200 discount fixed 20' => ['DE123456789', 20, 'fixed', 214.2, 200],
            'Italian tax price 200 discount percentage 20' => ['IT12345678900', 20, 'percentage', 195.2, 200],
            'Italian tax price 200 discount fixed 20' => ['IT12345678900', 20, 'fixed', 219.6, 200],
            'French tax price 200 discount percentage 20' => ['FRAA123456789', 20, 'percentage', 192, 200],
            'French tax price 200 discount fixed 20' => ['FRAA123456789', 20, 'fixed', 216, 200],
            'Greek tax price 200 discount percentage 20' => ['GR123456789', 20, 'percentage', 198.4, 200],
            'Greek tax price 200 discount fixed 20' => ['GR123456789', 20, 'fixed', 223.2, 200],
        ];
    }


    #[DataProvider('calculateProvider')]
    public function testCalculateSuccess($taxNumber, $discountValue, $discountType, $exceptedValue, $productPrice): void
    {
        $productRepo = $this->createMock(EntityRepository::class);
        $productRepo
            ->method('find')
            ->willReturn((new Product())->setPrice($productPrice));

        $couponRepo = $this->createMock(EntityRepository::class);
        $couponRepo
            ->method('findOneBy')
            ->willReturn((new Coupon())->setType($discountType)->setValue($discountValue));

        $this->entityManager
            ->method('getRepository')
            ->willReturnMap([
                [Product::class, $productRepo],
                [Coupon::class, $couponRepo],
            ]);

        $price = $this->priceCalculator->calculate(1, $taxNumber, 'code');
        $this->assertEquals($exceptedValue, $price);
    }

    public function testProductNotFound(): void
    {
        $productRepo = $this->createMock(EntityRepository::class);
        $productRepo->method('find')->willReturn(null);

        $this->entityManager
            ->method('getRepository')
            ->willReturn($productRepo);

        $this->expectException(\InvalidArgumentException::class);
        $this->priceCalculator->calculate(999, 'DE123456789');
    }

    protected function setUp(): void
    {
        $this->entityManager = $this->createMock(EntityManagerInterface::class);
        $this->taxCalculator = new TaxCalculator();
        $this->priceCalculator = new PriceCalculator(
            $this->entityManager,
            $this->taxCalculator
        );
    }
} 