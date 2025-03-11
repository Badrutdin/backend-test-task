<?php

namespace App\Service\PaymentProcessor;

use Systemeio\TestForCandidates\PaymentProcessor\StripePaymentProcessor;

class StripeProcessorAdapter implements PaymentProcessorInterface
{
    public function __construct(
        private StripePaymentProcessor $processor
    ) {}

    public function process(float $amount): bool
    {
        return $this->processor->processPayment($amount);
    }

    public static function getType(): string
    {
        return 'stripe';
    }
}