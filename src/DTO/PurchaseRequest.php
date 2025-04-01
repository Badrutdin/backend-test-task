<?php

namespace App\DTO;

use App\Entity\Coupon;
use App\Entity\Product;
use App\Enum\PaymentProcessTypeEnum;
use Symfony\Component\Validator\Constraints as Assert;

class PurchaseRequest
{
    #[Assert\NotBlank]
    private Product $product;
    #[Assert\NotBlank]
    #[Assert\Choice(callback: [PaymentProcessTypeEnum::class, 'getValues'])]
    private string $paymentProcessor;
    #[Assert\NotBlank]
    #[Assert\Regex(
        pattern: '/^(DE\d{9}|IT\d{11}|GR\d{9}|FR[A-Z]{2}\d{9})$/',
        message: 'Tax number must start with a valid country code (DE, IT, GR, FR) and the correct length.'
    )]
    private string $taxNumber;
    private ?Coupon $couponCode = null;

    public function getProduct(): Product
    {
        return $this->product;
    }

    public function setProduct(Product $product): self
    {
        $this->product = $product;
        return $this;
    }

    // Геттеры и сеттеры

    public function getPaymentProcessor(): string
    {
        return $this->paymentProcessor;
    }

    public function setPaymentProcessor(string $paymentProcessor): self
    {
        $this->paymentProcessor = $paymentProcessor;
        return $this;
    }

    public function getTaxNumber(): string
    {
        return $this->taxNumber;
    }

    public function setTaxNumber(string $taxNumber): self
    {
        $this->taxNumber = $taxNumber;
        return $this;
    }

    public function getCoupon(): ?Coupon
    {
        return $this->couponCode;
    }

    public function setCoupon(?Coupon $couponCode): self
    {
        $this->couponCode = $couponCode;
        return $this;
    }

} 