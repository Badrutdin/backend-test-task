<?php

namespace App\Resolver;

use App\DTO\CalculatePriceRequest;
use App\Entity\Coupon;
use App\Entity\Product;
use Doctrine\ORM\EntityManagerInterface;
use InvalidArgumentException;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Controller\ValueResolverInterface;
use Symfony\Component\HttpKernel\ControllerMetadata\ArgumentMetadata;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Validator\Exception\ValidationFailedException;
use Symfony\Component\Validator\Validator\ValidatorInterface;

/**
 * Class CalculatePriceRequestValueResolver
 *
 * Resolves a CalculatePriceRequest DTO from the incoming request, validates it,
 * and returns it if all validations pass.
 */
class CalculatePriceRequestValueResolver implements ValueResolverInterface
{
    private EntityManagerInterface $entityManager;
    private SerializerInterface $serializer;
    private ValidatorInterface $validator;


    public function __construct(
        EntityManagerInterface $entityManager,
        SerializerInterface $serializer,
        ValidatorInterface $validator
    ) {
        $this->entityManager = $entityManager;
        $this->serializer = $serializer;
        $this->validator = $validator;
    }

    public function resolve(Request $request, ArgumentMetadata $argument): iterable
    {
        if ($argument->getType() !== CalculatePriceRequest::class) {
            throw new InvalidArgumentException(
                'Resolver argument is not an instance of CalculatePriceRequest', 400
            );
        }

        $data = $this->serializer->decode($request->getContent(), 'json', []);
        if (empty($data)) {
            throw new InvalidArgumentException('Request data is empty', 400);
        }

        $productId = $data['productId'] ?? null;

        if (gettype($productId) !== 'integer') {
            throw new \InvalidArgumentException(
                'Product with ID is required and must be of type integer',
                400
            );
        }
        $product = $this->entityManager->getRepository(Product::class)->find($productId);

        if (!$product) {
            throw new InvalidArgumentException(
                sprintf('Product with ID %s not found', $productId),
                400
            );
        }

        $couponCode = $data['couponCode'] ?? null;
        $coupon = null;
        if ($couponCode) {
            $coupon = $this->entityManager->getRepository(Coupon::class)->findOneBy(['code' => $couponCode]);
            if (!$coupon) {
                throw new InvalidArgumentException(
                    sprintf('Coupon with code %s not found', $couponCode),
                    400
                );
            }
        }

        $dto = new CalculatePriceRequest();
        $dto->setProduct($product);
        $dto->setCoupon($coupon);

        $this->serializer->denormalize($data, CalculatePriceRequest::class, null, ['object_to_populate' => $dto]);

        $errors = $this->validator->validate($dto);
        if (count($errors) > 0) {
            throw new ValidationFailedException($dto, $errors);
        }

        yield $dto;
    }
}
