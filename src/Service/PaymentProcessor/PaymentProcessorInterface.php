<?php

namespace App\Service\PaymentProcessor;

interface PaymentProcessorInterface
{
    public function process(float $amount): bool;
} 