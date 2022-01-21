<?php

namespace App\Controller;

use App\Entity\Cart;
use App\Entity\Product;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/api/cart")
 */
class CartController extends AbstractController
{
    private $entityManager;

    public function __construct(ManagerRegistry $doctrine)
    {
        $this->entityManager = $doctrine->getManager();
    }

    /**
     * @Route("", methods={"POST"})
     */
    public function createCart(): Response
    {
        $cart = new Cart();

        $this->entityManager->persist($cart);
        $this->entityManager->flush();

        return $this->json([
            "code" => Response::HTTP_OK,
            "id" => $cart->getId(),
        ]);
    }

    /**
     * @Route("/{cartId}/add-product/{productId}", methods={"PUT"}, requirements={"cartId"="\d+", "productId"="\d+"})
     */
    public function addProductToCart(int $cartId, int $productId): Response
    {
        $cart = $this->entityManager->getRepository(Cart::class)->find($cartId);

        if (!$cart)
            return $this->badJsonResponse("Cart with given id does not exist.");

        $product = $this->entityManager->getRepository(Product::class)->find($productId);

        if (!$product)
            return $this->badJsonResponse("Product with given id does not exist.");

        $cartsProducts = $cart->getProducts();

        if ($cartsProducts->contains($product))
            return $this->badJsonResponse("Cart already has that product.");

        if (count($cartsProducts) >= 3)
            return $this->badJsonResponse("Cart has already maximum (three) number of products in it.");

        $cart->addProduct($product);
        $this->entityManager->persist($cart);
        $this->entityManager->flush();

        return $this->okJsonResponse("Product $productId added successfully to $cartId cart.");
    }

    /**
     * @Route("/{cartId}/remove-product/{productId}", methods={"PUT"}, requirements={"cartId"="\d+", "productId"="\d+"})
     */
    public function removeProductFromCart(int $cartId, int $productId): Response
    {
        $cart = $this->entityManager->getRepository(Cart::class)->find($cartId);

        if (!$cart)
            return $this->badJsonResponse("Cart with given id does not exist.");

        $product = $this->entityManager->getRepository(Product::class)->find($productId);

        if (!$product)
            return $this->badJsonResponse("Product with given id does not exist.");

        $cartsProducts = $cart->getProducts();

        if (!$cartsProducts->contains($product))
            return $this->badJsonResponse("Cart with given id does not have given product.");

        $cart->removeProduct($product);

        $this->entityManager->persist($cart);
        $this->entityManager->flush();

        return $this->okJsonResponse("Product $productId removed successfully from $cartId cart.");
    }

    /**
     * @Route("/{cartId}/list", methods={"GET"}, requirements={"cartId"="\d+"})
     */
    public function listProductsInCart(int $cartId): Response
    {
        $cart = $this->entityManager->getRepository(Cart::class)->find($cartId);

        if (!$cart)
            return $this->badJsonResponse("Cart with given id does not exist.");

        $products = $cart->getProducts();
        $sum = 0;
        $productArray = [];

        foreach ($products as $var) {
            $sum += (int)(((float)explode(" ", $var->getPrice())[0]) * 100);
            $productArray[] = [
                "id" => $var->getId(),
                "title" => $var->getTitle(),
                "price" => $var->getPrice(),
                "cart" => $var->getCart()->getId(),
            ];
        }

        return $this->json([
            "code" => Response::HTTP_OK,
            "products" => $productArray,
            "total_price" => strval($sum > 0 ? $sum / 100 : 0.00)." USD"
        ]);
    }

    public function okJsonResponse(string $message): Response
    {
        return $this->json([
            "code" => Response::HTTP_OK,
            "message" => $message,
        ]);
    }

    public function badJsonResponse(string $message): Response
    {
        return $this->json([
            "code" => Response::HTTP_BAD_REQUEST,
            "error" => $message,
        ], Response::HTTP_BAD_REQUEST);
    }
}
