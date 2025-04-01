<?php

namespace App\DataFixtures;

use App\Entity\Product;
use App\Entity\Coupon;
use App\Enum\CouponTypeEnum;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class AppFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        // Создаем продукты
        $iphone = new Product();
        $iphone->setName('Iphone');
        $iphone->setPrice(100);
        $manager->persist($iphone);

        $headphones = new Product();
        $headphones->setName('Наушники');
        $headphones->setPrice(20);
        $manager->persist($headphones);

        $case = new Product();
        $case->setName('Чехол');
        $case->setPrice(10);
        $manager->persist($case);

        // Создаем купоны
        $percentCoupon = new Coupon();
        $percentCoupon->setCode('P15');
        $percentCoupon->setType(CouponTypeEnum::PERCENTAGE->value);
        $percentCoupon->setValue(15);
        $manager->persist($percentCoupon);

        $fixedCoupon = new Coupon();
        $fixedCoupon->setCode('F100');
        $fixedCoupon->setType(CouponTypeEnum::FIXED->value);
        $fixedCoupon->setValue(100);
        $manager->persist($fixedCoupon);

        $manager->flush();
    }
} 