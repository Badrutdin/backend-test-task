<?php

namespace Service;

use App\Entity\Coupon;
use App\Entity\Product;
use App\Service\PriceCalculator;
use App\Service\TaxCalculator;
use Doctrine\ORM\EntityManagerInterface;
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
        $product = (new Product())->setPrice($productPrice);
        $coupon = (new Coupon())->setType($discountType)->setValue($discountValue);

        $price = $this->priceCalculator->calculate($product, $taxNumber, $coupon);
        $this->assertEquals($exceptedValue, $price);
    }


    protected function setUp(): void
    {
        $this->entityManager = $this->createMock(EntityManagerInterface::class);
        $this->taxCalculator = new TaxCalculator();
        $this->priceCalculator = new PriceCalculator(
            $this->taxCalculator
        );
    }
} 