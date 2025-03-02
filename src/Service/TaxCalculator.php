<?php

namespace App\Service;

class TaxCalculator
{
    private const TAX_RATES = [
        'DE' => 0.19, // 19%
        'IT' => 0.22, // 22%
        'FR' => 0.20, // 20%
        'GR' => 0.24, // 24%
    ];

    public function calculateTax(float $price, string $taxNumber): float
    {
        $countryCode = substr($taxNumber, 0, 2);
        if (!isset(self::TAX_RATES[$countryCode])) {
            throw new \InvalidArgumentException('Invalid country code');
        }

        return $price * self::TAX_RATES[$countryCode];
    }
} 