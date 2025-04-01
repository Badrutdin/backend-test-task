<?php

namespace App\Controller;

use App\DTO\PurchaseRequest;
use App\Resolver\PurchaseRequestValueResolver;
use App\Service\PaymentProcessor\PaymentProcessorFactory;
use App\Service\PriceCalculator;
use InvalidArgumentException;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Attribute\MapRequestPayload;
use Symfony\Component\Routing\Annotation\Route;


#[Route('/purchase', name: 'app_purchase', methods: ['POST'])]
class PurchaseController extends AbstractController
{
    public function __construct(
        private PriceCalculator         $priceCalculator,
        private PaymentProcessorFactory $processorFactory
    )
    {
    }

    public function __invoke(
        #[MapRequestPayload(resolver: PurchaseRequestValueResolver::class)] PurchaseRequest $purchaseRequest
    ): Response
    {
        $finalPrice = $this->priceCalculator->calculate(
            $purchaseRequest->getProduct(),
            $purchaseRequest->getTaxNumber(),
            $purchaseRequest->getCoupon()
        );

        $processor = $this->processorFactory->create($purchaseRequest->getPaymentProcessor());
        $success = $processor->process($finalPrice);

        if (!$success) {
            throw new InvalidArgumentException('Payment failed', 400);
        }

        return $this->json(['status' => 'success']);
    }
}