<?php

namespace App\Service;

use App\Entity\Coupon;
use App\Entity\Product;
use App\Enum\CouponTypeEnum;

readonly class PriceCalculator
{
    public function __construct(
        private TaxCalculator $taxCalculator
    ) {
    }

    public function calculate(Product $product, string $taxNumber, ?Coupon $coupon = null): float
    {


        $price = $product->getPrice();

        if ($coupon) {
            $discount = $coupon->getType() === CouponTypeEnum::PERCENTAGE->value
                ? $price * $coupon->getValue() / 100
                : $coupon->getValue();

            $price = max(0, $price - $discount);
        }

        $tax = $this->taxCalculator->calculateTax($price, $taxNumber);

        return round($price + $tax, 2);
    }
} 