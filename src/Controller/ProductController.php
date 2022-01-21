<?php

namespace App\Controller;

use App\Entity\Product;
use App\Repository\ProductRepository;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Validator\ConstraintViolationListInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;

// *     condition="request.headers.get('Content-Type') === 'application/json'"

/**
 * @Route("/api/product")
 */
class ProductController extends AbstractController
{
    private $validator;
    private $entityManager;

    public function __construct(ValidatorInterface $validator, ManagerRegistry $doctrine)
    {
        $this->validator = $validator;
        $this->entityManager = $doctrine->getManager();
    }

    /**
     * Gets 3 products on specified page.
     * @Route("/list/{page?1}", methods={"GET"}, requirements={"page"="\d+"})
     */
    public function getProductList(Request $request, int $page): Response
    {
        $repository = $this->entityManager->getRepository(Product::class);

        if ($repository instanceof ProductRepository) {
            $products = $repository->getProductsOnPage($page);

            if (!$products)
                return $this->badResponseJson("There is no products on that page :(");

            return $this->json([
                "code" => Response::HTTP_OK,
                "products" => $products
            ]);
        } else {
            return $this->badResponseJson("Internal problems x_x, working on that...");
        }
    }

    /**
     * Adds a product.
     * @Route("", methods={"POST"})
     */
    public function addProduct(Request $request): Response
    {
        $title = $request->get('title');
        $price = $request->get('price');

        if (is_null($title) || is_null($price))
            return $this->badResponseJson("Both title and price fields must be set correctly.");

        $product = new Product();
        $product->setPrice($price);
        $product->setTitle($title);

        $errors = $this->validator->validate($product);

        if (count($errors) > 0)
            return $this->badResponseJson($errors);

        $this->entityManager->persist($product);
        $this->entityManager->flush();

        return $this->okResponseJson(Response::HTTP_OK, $product);
    }

    /**
     * Updates a product.
     * @Route("/{id}", methods={"PUT"}, requirements={"id"="\d+"})
     */
    public function updateProduct(int $id, Request $request): Response
    {
        $product = $this->entityManager->getRepository(Product::class)->find($id);

        if (!$product)
            return $this->badResponseJson("Product with that id does not exist.");

        $title = $request->get('title');
        $price = $request->get('price');

        if (!is_null($title))
            $product->setTitle($title);

        if (!is_null($price))
            $product->setPrice($price);

        $errors = $this->validator->validate($product);

        if (count($errors) > 0)
            return $this->badResponseJson($errors);

        $this->entityManager->flush();

        return $this->okResponseJson(Response::HTTP_OK, $product);
    }

    /**
     * Gets a product based on id.
     * @Route("/{id}", methods={"GET"}, requirements={"id"="\d+"})
     */
    public function getProduct(int $id = 1): Response
    {
        $product = $this->entityManager->getRepository(Product::class)->find($id);

        if (!$product)
            return $this->badResponseJson("Product with that id does not exist.");

        return $this->okResponseJson(Response::HTTP_OK, $product);
    }

    /**
     * Removes a product.
     * @Route("/{id}", methods={"DELETE"}, requirements={"id"="\d+"})
     */
    public function removeProduct(int $id): Response
    {
        $product = $this->entityManager->getRepository(Product::class)->find($id);

        if (!$product)
            return $this->badResponseJson("Product with that id does not exist.");

        $this->entityManager->remove($product);
        $this->entityManager->flush();

        return $this->okResponseJson(Response::HTTP_OK, $product);
    }

    /**
     * Json response for correct REST API request.
     * @param $code
     * @param  Product  $product
     * @return Response
     */
    public function okResponseJson($code, Product $product): Response
    {
        return $this->json([
            "code" => $code,
            "id" => $product->getId(),
            "title" => $product->getTitle(),
            "price" => $product->getPrice(),
            "cart" => $product->getCart(),
        ], $code);
    }

    /**
     * Json response for incorrect REST API request.
     * @param $errors
     * @return JsonResponse
     */
    public function badResponseJson($errors): Response
    {
        $message = "";

        if ($errors instanceof ConstraintViolationListInterface)
            foreach ($errors as $v)
                $message .= " " . $v->getMessage();

        return $this->json([
            "code" => Response::HTTP_BAD_REQUEST,
            "error" => !$message ? $errors : $message,
        ], Response::HTTP_BAD_REQUEST);
    }
}
