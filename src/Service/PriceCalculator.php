<?php

namespace App\Service;

use App\Entity\Coupon;
use App\Entity\Product;
use Doctrine\ORM\EntityManagerInterface;

class PriceCalculator
{
    public function __construct(
        private EntityManagerInterface $entityManager,
        private TaxCalculator          $taxCalculator
    )
    {
    }

    public function calculate(int $productId, string $taxNumber, ?string $couponCode = null): float
    {
        $product = $this->entityManager->getRepository(Product::class)->find($productId);
        if (!$product) {
            throw new \InvalidArgumentException('Product not found');
        }

        $price = $product->getPrice();

        if ($couponCode) {
            $coupon = $this->entityManager->getRepository(Coupon::class)->findOneBy(['code' => $couponCode]);
            if (!$coupon) {
                throw new \InvalidArgumentException('Invalid coupon code');
            }

            $discount = $coupon->getType() === 'percentage'
                ? $price * $coupon->getValue() / 100
                : $coupon->getValue();

            $price = max(0, $price - $discount);
        }

        $tax = $this->taxCalculator->calculateTax($price, $taxNumber);

        return round($price + $tax, 2);
    }
} 