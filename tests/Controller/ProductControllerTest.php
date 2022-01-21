<?php

namespace App\Tests\Controller;

use App\Entity\Product;
use Doctrine\Persistence\ObjectManager;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class ProductControllerTest extends WebTestCase
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
    // POST /api/product
    //

    public function testAddingProductCorrectly(): void
    {
        $this->client->xmlHttpRequest('POST', '/api/product', [
            "title" => "Tea",
            "price" => "3.99 USD"
        ]);
        $this->assertResponseStatusCodeSame(200);
    }

    public function testAddingProductWithoutAField(): void
    {
        $this->client->xmlHttpRequest('POST', '/api/product', [
            "price" => "3.99 USD"
        ]);
        $this->assertResponseStatusCodeSame(400);
    }

    public function testAddingProductWithNoFields(): void
    {
        $this->client->xmlHttpRequest('POST', '/api/product');
        $this->assertResponseStatusCodeSame(400);
    }

    public function testAddingProductWithIncorrectPrice(): void
    {
        $this->client->xmlHttpRequest('POST', '/api/product', [
            "price" => "3.99 USeD",
            "title" => "Joe Mamma"
        ]);

        $this->assertResponseStatusCodeSame(400);
    }

    //
    // GET /api/product | /api/product/x
    //

    public function testGettingProductCorrectly(): void
    {
        $this->client->request('GET', '/api/product/1');
        $this->assertResponseStatusCodeSame(200);
//        $this->assertJsonStringEqualsJsonString(
//            '{"code":200,"id":1,"title":"Chocolate","price":"1.99 USD"}',
//            $this->client->getResponse()->getContent()
//        );
    }

    public function testGettingProductThatDoesntExist(): void
    {
        $this->client->request('GET', '/api/product/999');
        $this->assertResponseStatusCodeSame(400);
    }

    //
    // PUT /api/product/x
    //

    public function testUpdatingProductCorrectly(): void
    {
        $this->client->request('PUT', '/api/product/1', [
            'title' => 'Joes'
        ]);
        $this->assertResponseStatusCodeSame(200);
    }

    public function testUpdatingProductThatDoesntExist(): void
    {
        $this->client->request('PUT', '/api/product/999', [
            'title' => 'Joes'
        ]);
        $this->assertResponseStatusCodeSame(400);
    }

    //
    // DELETE /api/product/x
    //

    public function testDeletingProductThatExistsThenCheckAgain(): void
    {
        $this->client->request('DELETE', '/api/product/5');
        $this->assertResponseStatusCodeSame(200);
        $this->client->request('DELETE', '/api/product/5');
        $this->assertResponseStatusCodeSame(400);
    }

    //
    // GET /api/product/list
    //

    public function testGettingPageOfProductsCorrectly(): void
    {
        $this->client->request('GET', '/api/product/list/1');

        $this->assertResponseStatusCodeSame(200);
    }

    public function testGettingPageOfProductsThatDoesNotHaveProducts(): void
    {
        $this->client->request('GET', '/api/product/list/3');

        $this->assertResponseStatusCodeSame(400);
    }

    public function testGettingPageOfProductsWithIndexZero(): void
    {
        $this->client->request('GET', '/api/product/list/0');

        $this->assertResponseStatusCodeSame(400);
    }
}
