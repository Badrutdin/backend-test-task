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
        $rate = $this->getCountryFromPattern($taxNumber);

        return $price * $rate;
    }

    private function getCountryFromPattern($taxNumber)
    {
        $pattern = '/^(DE\d{9}|IT\d{11}|GR\d{9}|FR[A-Z]{2}\d{9})$/';

        if (preg_match($pattern, $taxNumber, $matches)) {
            $countryCode = substr($taxNumber, 0, 2);


            return self::TAX_RATES[$countryCode];
        } else {

            throw new \InvalidArgumentException('Tax number must start with a valid country code (DE, IT, GR, FR) and the correct length.');
        }
    }
} 