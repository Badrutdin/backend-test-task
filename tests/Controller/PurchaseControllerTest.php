<?php

namespace Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\JsonResponse;

class PurchaseControllerTest extends WebTestCase
{
    public function testPurchaseSuccess(): void
    {
        $client = static::createClient();

        $data = [
            'product' => 1,
            'paymentProcessor' => 'paypal',
            'taxNumber' => 'DE123456789',
            'couponCode' => 'F100'
        ];

        $client->request(
            method: 'POST',
            uri: '/purchase',
            server: ['CONTENT_TYPE' => 'application/json'],
            content: json_encode($data)
        );

        $response = $client->getResponse();

        $this->assertInstanceOf(JsonResponse::class, $response);
        $this->assertEquals(200, $response->getStatusCode());
        $this->assertJsonStringEqualsJsonString(json_encode(['status' => 'success']), $response->getContent());
    }

    public function testPurchaseValidationError(): void
    {
        $client = static::createClient();

        $data = [
            'product' => 1,
            'paymentProcessor' => 'paypal',
            'taxNumber' => 'DE1234567891',
            'couponCode' => 'F100'
        ];

        $client->request(
            method: 'POST',
            uri: '/purchase',
            server: ['CONTENT_TYPE' => 'application/json'],
            content: json_encode($data)
        );

        $response = $client->getResponse();

        $this->assertEquals(400, $response->getStatusCode());

        $responseData = json_decode($response->getContent(), true);
        $this->assertArrayHasKey('errors', $responseData);
    }

    public function testPurchaseDeserializationError(): void
    {
        $client = static::createClient();

        $data = [
            'product' => 'INVALID_PRODUCT_TYPE',
            'paymentProcessor' => 'paypal',
            'taxNumber' => 'DE123456789',
            'couponCode' => 'F100'
        ];

        $client->request(
            method: 'POST',
            uri: '/purchase',
            server: ['CONTENT_TYPE' => 'application/json'],
            content: json_encode($data)
        );

        $response = $client->getResponse();

        $this->assertEquals(400, $response->getStatusCode());

        $responseData = json_decode($response->getContent(), true);
        $this->assertArrayHasKey('error', $responseData);
    }

    public function testPurchasePaymentFailed(): void
    {
        $client = static::createClient();

        $data = [
            'product' => 1,
            'paymentProcessor' => 'stripe',
            'taxNumber' => 'DE123456789',
            'couponCode' => 'F100'
        ];

        $client->request(
            method: 'POST',
            uri: '/purchase',
            server: ['CONTENT_TYPE' => 'application/json'],
            content: json_encode($data));

        $response = $client->getResponse();

        $this->assertEquals(400, $response->getStatusCode());
        $this->assertJsonStringEqualsJsonString(json_encode(['error' => 'Payment failed']), $response->getContent());
    }
}