<?php

namespace App\Service;

use App\Enum\TaxCountryEnum;
use Symfony\Component\Validator\Exception\InvalidArgumentException;


class TaxCalculator
{


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


            $country = TaxCountryEnum::from($countryCode);

            return $country->getTaxRate();
        } else {

            throw new InvalidArgumentException(
                'Tax number must start with a valid country code (DE, IT, GR, FR) and the correct length.',
                400
            );
        }
    }
} 