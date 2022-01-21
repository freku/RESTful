<?php

namespace App\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class CartControllerTest extends WebTestCase
{
    private $client;

    protected function setUp(): void
    {
        self::ensureKernelShutdown();
        $this->client = static::createClient([], [
            "HTTP_HOST" => 'localhost:8000',
        ]);

        self::bootKernel();
    }

    //
    // POST
    //

    public function testCreatingNewCart(): void
    {
        $this->client->xmlHttpRequest('POST', '/api/cart');
        $this->assertResponseStatusCodeSame(200);
    }

    //
    // PUT add-product
    //

    public function testAddingProductToCartCorrectly(): void
    {
        $this->client->xmlHttpRequest('PUT', '/api/cart/1/add-product/1');
        $this->assertResponseStatusCodeSame(200);
    }

    public function testAddingProductToCartThatAlreadyHasIt(): void
    {
        $this->client->xmlHttpRequest('PUT', '/api/cart/1/add-product/1');
        $this->assertResponseStatusCodeSame(200);

        $this->client->xmlHttpRequest('PUT', '/api/cart/1/add-product/1');
        $this->assertResponseStatusCodeSame(400);
    }

    public function testAddingNonExistingProductToCart(): void
    {
        $this->client->xmlHttpRequest('PUT', '/api/cart/1/add-product/0');
        $this->assertResponseStatusCodeSame(400);
    }

    public function testAddingProductToNonExistingCart(): void
    {
        $this->client->xmlHttpRequest('PUT', '/api/cart/1/add-product/999');
        $this->assertResponseStatusCodeSame(400);
    }

    public function testAddingMoreThanThreeProductsToCart(): void
    {
        $this->client->xmlHttpRequest('PUT', '/api/cart/1/add-product/1');
        $this->client->xmlHttpRequest('PUT', '/api/cart/1/add-product/2');
        $this->client->xmlHttpRequest('PUT', '/api/cart/1/add-product/3');
        $this->assertResponseStatusCodeSame(200);
        $this->client->xmlHttpRequest('PUT', '/api/cart/1/add-product/4');
        $this->assertResponseStatusCodeSame(400);
    }

    //
    // PUT remove-product
    //

    public function testRemovingProductFromCartThatHasIt(): void
    {
        $this->client->xmlHttpRequest('PUT', '/api/cart/1/add-product/1');
        $this->assertResponseStatusCodeSame(200);
        $this->client->xmlHttpRequest('PUT', '/api/cart/1/remove-product/1');
        $this->assertResponseStatusCodeSame(200);
    }

    public function testRemovingProductFromCartThatDoesntHaveIt(): void
    {
        $this->client->xmlHttpRequest('PUT', '/api/cart/1/remove-product/1');
        $this->assertResponseStatusCodeSame(400);
    }

    public function testRemovingNonExistingProductFromCart(): void
    {
        $this->client->xmlHttpRequest('PUT', '/api/cart/1/remove-product/99');
        $this->assertResponseStatusCodeSame(400);
    }

    public function testRemovingProductFromNonExistingCart(): void
    {
        $this->client->xmlHttpRequest('PUT', '/api/cart/99/remove-product/1');
        $this->assertResponseStatusCodeSame(400);
    }

    //
    // GET /list
    //

    public function testGettingListOfCartsProducts(): void
    {
        $this->client->xmlHttpRequest('GET', '/api/cart/1/list');
        $this->assertResponseStatusCodeSame(200);
    }
}
