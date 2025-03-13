<?php

namespace App\Controller;

use App\DTO\CalculatePriceRequest;
use App\Service\PriceCalculator;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;

#[Route('/calculate-price', name: 'app_calculate_price', methods: ['POST'])]
class CalculateController extends AbstractController
{
    public function __construct(
        private PriceCalculator     $priceCalculator,
        private SerializerInterface $serializer,
        private ValidatorInterface  $validator
    )
    {
    }

    public function __invoke(Request $request): Response
    {
        try {
            $calculateRequest = $this->serializer->deserialize(
                $request->getContent(),
                CalculatePriceRequest::class,
                'json'
            );

            $violations = $this->validator->validate($calculateRequest);

            if (count($violations) > 0) {
                $errorMessages = [];
                foreach ($violations as $violation) {
                    $errorMessages[$violation->getPropertyPath()] = $violation->getMessage() . ' Received: ' . $violation->getInvalidValue();
                }
                return $this->json(['errors' => $errorMessages], Response::HTTP_BAD_REQUEST);
            }

            $price = $this->priceCalculator->calculate(
                $calculateRequest->getProduct(),
                $calculateRequest->getTaxNumber(),
                $calculateRequest->getCouponCode()
            );

            return $this->json(['price' => $price]);

        } catch (\Exception $e) {
            return $this->json(['error' => $e->getMessage()], Response::HTTP_BAD_REQUEST);
        }
    }
}