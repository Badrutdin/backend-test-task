<?php

namespace App\Service\PaymentProcessor;

use Systemeio\TestForCandidates\PaymentProcessor\PaypalPaymentProcessor;

class PaypalProcessorAdapter implements PaymentProcessorInterface
{
    public function __construct(
        private PaypalPaymentProcessor $processor
    ) {}

    public function process(float $amount): bool
    {
        try {
            $amountInCents = (int)($amount * 100);
            $this->processor->pay($amountInCents);
            return true;
        } catch (\Exception $e) {
            return false;
        }
    }

    public static function getType(): string
    {
        return 'paypal';
    }
}