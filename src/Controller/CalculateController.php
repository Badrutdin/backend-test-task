<?php

namespace App\Controller;

use App\DTO\CalculatePriceRequest;
use App\Resolver\CalculatePriceRequestValueResolver;
use App\Service\PriceCalculator;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Attribute\MapRequestPayload;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/calculate-price', name: 'app_calculate_price', methods: ['POST'])]
class CalculateController extends AbstractController
{
    public function __construct(
        private readonly PriceCalculator $priceCalculator
    )
    {
    }

    public function __invoke(#[MapRequestPayload(resolver: CalculatePriceRequestValueResolver::class)]
                             CalculatePriceRequest $calculateRequest): Response
    {

        $price = $this->priceCalculator->calculate(
            $calculateRequest->getProduct(),
            $calculateRequest->getTaxNumber(),
            $calculateRequest->getCoupon()
        );
        return $this->json(['price' => $price]);

    }
}