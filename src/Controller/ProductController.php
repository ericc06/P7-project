<?php

namespace App\Controller;

use App\Entity\Product;
use App\Exception\ResourceValidationException;
use App\Representation\Products;
use FOS\RestBundle\Controller\Annotations as Rest;
use FOS\RestBundle\Request\ParamFetcherInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Symfony\Component\Routing\Annotation\Route;
use FOS\RestBundle\Controller\AbstractFOSRestController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Psr\Log\LoggerInterface;

//use Hateoas\Representation\PaginatedRepresentation;

/**
 * Product controller.
 * @Route("/api", name="api_")
 */
class ProductController extends AbstractFOSRestController
{
    /**
     * @Rest\Get("/products", name="api_product_list")
     * @Rest\QueryParam(
     *     name="brand",
     *     requirements="[a-zA-Z0-9]+",
     *     nullable=true,
     *     description="The brand name to search for."
     * )
     * @Rest\QueryParam(
     *     name="order",
     *     requirements="asc|desc",
     *     default="asc",
     *     description="Sort order (asc or desc)."
     * )
     * @Rest\QueryParam(
     *     name="limit",
     *     requirements="\d+",
     *     default="10",
     *     description="Max number of products per page."
     * )
     * @Rest\QueryParam(
     *     name="page",
     *     requirements="\d+",
     *     default="1",
     *     description="The requested paginated page."
     * )
     * @Rest\View
     */
    public function list(ParamFetcherInterface $paramFetcher)
    {
        $pager = $this->getDoctrine()->getRepository(Product::class)->search(
            $paramFetcher->get('brand'),
            $paramFetcher->get('order'),
            $paramFetcher->get('limit'),
            $paramFetcher->get('page')
        );

        return new Products($pager);
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
