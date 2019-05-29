<?php

namespace App\Controller;

use App\Entity\Product;
use App\Exception\ResourceValidationException;
use App\Representation\Products;
use FOS\RestBundle\Controller\Annotations as Rest;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use FOS\RestBundle\Controller\FOSRestController;
use Symfony\Component\HttpFoundation\Response;

//use Hateoas\Representation\PaginatedRepresentation;

/**
 * Product controller.
 * @Route("/api", name="api_")
 */
class ProductController extends FOSRestController
{
    /**
     * @Rest\Get("/products", name="api_product_list")
     * @Rest\View
     */
    public function list()
    {
        $products = $this->getDoctrine()->getRepository(Product::class)->findAll();
        $data = $this->get('jms_serializer')->serialize($products, 'json');

        $response = new Response($data);
        $response->headers->set('Content-Type', 'application/json');

        return $response;
    }

    /**
     * @Rest\Get(
     *     path = "/products/{id}",
     *     name = "api_product_show",
     *     requirements = {"id"="\d+"}
     * )
     * @Rest\View
     */
    public function show(Product $product)
    {
        return $product;
    }
}
