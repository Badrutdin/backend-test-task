<?php

namespace App\DTO;

use Symfony\Component\Validator\Constraints as Assert;

class PurchaseRequest
{
    #[Assert\NotBlank]
    #[Assert\Positive]
    private int $product;

    #[Assert\NotBlank]
    #[Assert\Choice(['paypal', 'stripe'])]
    private string $paymentProcessor;

    #[Assert\NotBlank]
    #[Assert\Regex(
        pattern: '/^(DE|IT|GR|FR)/',
        message: 'Tax number must start with a valid country code (DE, IT, GR, FR)'
    )]
    private string $taxNumber;

    #[Assert\Length(min: 3)]
    private ?string $couponCode = null;

    // Геттеры и сеттеры
    public function getProduct(): int
    {
        return $this->product;
    }

    public function setProduct(int $product): self
    {
        $this->product = $product;
        return $this;
    }

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

    public function getCouponCode(): ?string
    {
        return $this->couponCode;
    }

    public function setCouponCode(?string $couponCode): self
    {
        $this->couponCode = $couponCode;
        return $this;
    }
} 