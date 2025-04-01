<?php

namespace App\Enum;

enum PaymentProcessTypeEnum: string
{
    case PAYPAL = 'paypal';
    case STRIPE = 'stripe';


    /**
     * @return array<int, string>
     */
    public static function getValues(): array
    {
        return array_map(fn(PaymentProcessTypeEnum $paymentProcessorEnum): string => $paymentProcessorEnum->value,
            self::cases());
    }

}
