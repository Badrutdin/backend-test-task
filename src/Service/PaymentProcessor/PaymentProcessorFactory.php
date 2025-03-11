<?php

namespace App\Service\PaymentProcessor;

use Symfony\Component\DependencyInjection\Attribute\TaggedIterator;

class PaymentProcessorFactory
{
    private array $processors = [];

    public function __construct(
        #[TaggedIterator('app.payment_processor', defaultIndexMethod: 'getType')]
        iterable $processors
    )
    {
        foreach ($processors as $type => $processor) {
            $this->processors[$type] = $processor;
        }

    }

    public function create(string $type): PaymentProcessorInterface
    {

        if (!isset($this->processors[$type])) {
            throw new \InvalidArgumentException("Invalid payment processor type: $type");
        }

        return $this->processors[$type];
    }
}