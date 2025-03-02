<?php

namespace App\Service\PaymentProcessor;

use Systemeio\TestForCandidates\PaymentProcessor\PaypalPaymentProcessor;
use Systemeio\TestForCandidates\PaymentProcessor\StripePaymentProcessor;

class PaymentProcessorFactory
{
    public function create(string $type): PaymentProcessorInterface
    {
        return match ($type) {
            'paypal' => new PaypalProcessorAdapter(new PaypalPaymentProcessor()),
            'stripe' => new StripeProcessorAdapter(new StripePaymentProcessor()),
            default => throw new \InvalidArgumentException('Invalid payment processor type'),
        };
    }
} 