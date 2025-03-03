<?php

namespace Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\JsonResponse;

class CalculateControllerTest extends WebTestCase
{
    public function testCalculateSuccess(): void
    {
        $client = static::createClient();

        $data = [
            'product' => 1,
            'taxNumber' => 'DE123456789',
            'couponCode' => 'P100'
        ];

        $client->request(
            method: 'POST',
            uri: '/calculate-price',
            server: ['CONTENT_TYPE' => 'application/json'],
            content: json_encode($data)
        );

        $response = $client->getResponse();

        $this->assertInstanceOf(JsonResponse::class, $response);
        $this->assertEquals(200, $response->getStatusCode());
        $result = json_decode($response->getContent(), true);
        $price = $result['price'];
        $this->assertArrayHasKey('price', $result, 'The response does not contain the "price" field.');
        $this->assertTrue(
            is_float($price) || $price === 0,
            'The "price" field must be float or 0'
        );
    }

    public function testCalculateDeserializationError(): void
    {
        $client = static::createClient();

        $data = [
            'product' => 'INVALID_PRODUCT_TYPE',
            'taxNumber' => 'DE123456789',
            'couponCode' => 'P100'
        ];

        $client->request(
            method: 'POST',
            uri: '/calculate-price',
            server: ['CONTENT_TYPE' => 'application/json'],
            content: json_encode($data)
        );

        $response = $client->getResponse();

        $this->assertEquals(400, $response->getStatusCode());
        $responseData = json_decode($response->getContent(), true);
        $this->assertArrayHasKey('error', $responseData);
    }

    public function testCalculateValidationError(): void
    {
        $client = static::createClient();

        $data = [
            'product' => 1,
            'taxNumber' => 'INVALID_TAX_NUMBER',
            'couponCode' => 'P100'
        ];

        $client->request(
            method: 'POST',
            uri: '/calculate-price',
            server: ['CONTENT_TYPE' => 'application/json'],
            content: json_encode($data)
        );

        $response = $client->getResponse();

        $this->assertEquals(400, $response->getStatusCode());
        $responseData = json_decode($response->getContent(), true);
        $this->assertArrayHasKey('errors', $responseData);
    }

}