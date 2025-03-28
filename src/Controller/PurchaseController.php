<?php

namespace App\Controller;

use App\DTO\PurchaseRequest;
use App\Service\PaymentProcessor\PaymentProcessorFactory;
use App\Service\PriceCalculator;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;

#[Route('/purchase', name: 'app_purchase', methods: ['POST'])]
class PurchaseController extends AbstractController
{
    public function __construct(
        private PriceCalculator         $priceCalculator,
        private PaymentProcessorFactory $processorFactory,
        private SerializerInterface     $serializer,
        private ValidatorInterface      $validator
    )
    {
    }

    public function __invoke(Request $request): Response
    {
        try {
            $purchaseRequest = $this->serializer->deserialize(
                $request->getContent(),
                PurchaseRequest::class,
                'json'
            );
            $violations = $this->validator->validate($purchaseRequest);

            if (count($violations) > 0) {
                $errorMessages = [];
                foreach ($violations as $violation) {
                    $errorMessages[$violation->getPropertyPath()] = $violation->getMessage() . ' Received: ' . $violation->getInvalidValue(); // Извлекаем сообщения о нарушениях
                }
                return $this->json(['errors' => $errorMessages], Response::HTTP_BAD_REQUEST);
            }
            $finalPrice = $this->priceCalculator->calculate(
                $purchaseRequest->getProduct(),
                $purchaseRequest->getTaxNumber(),
                $purchaseRequest->getCouponCode()
            );
            $processor = $this->processorFactory->create($purchaseRequest->getPaymentProcessor());
            $success = $processor->process($finalPrice);

            if (!$success) throw new \Exception('Payment failed');

            return $this->json(['status' => 'success']);
        } catch (\Exception $e) {
            return $this->json(['error' => $e->getMessage()], Response::HTTP_BAD_REQUEST);
        }
    }
}