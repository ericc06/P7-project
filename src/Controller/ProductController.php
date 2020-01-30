<?php

namespace App\Controller;

use App\Entity\Product;
use App\Representation\Products;
use FOS\RestBundle\Controller\Annotations as Rest;
use FOS\RestBundle\Request\ParamFetcherInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Symfony\Component\Routing\Annotation\Route;
use FOS\RestBundle\Controller\AbstractFOSRestController;
use Symfony\Component\HttpFoundation\Response;
use Nelmio\ApiDocBundle\Annotation\Security;
use Swagger\Annotations as SWG;
use App\Tools\Tools;

/**
 * Product controller.
 * @Route("/api", name="api_")
 */
class ProductController extends AbstractFOSRestController
{
    private $tools;

    public function __construct(Tools $tools)
    {
        $this->tools = $tools;
    }

    /**
     * @Rest\Get("/products", name="product_list")
     * @Rest\QueryParam(
     *     name="brand",
     *     requirements={
     *         "rule" = "[a-zA-Z0-9]+",
     *         "error_message" = "'brand' must contain only letters or figures."
     *     },
     *     strict=true,
     *     nullable=true,
     *     description="(optional) The brand name to search for."
     * )
     * @Rest\QueryParam(
     *     name="order",
     *     requirements={
     *         "rule" = "asc|desc",
     *         "error_message" = "'order' must be 'asc' or 'desc'"
     *     },
     *     strict=true,
     *     nullable=true,
     *     description="(optional) Sort order (asc or desc)."
     * )
     * @Rest\QueryParam(
     *     name="limit",
     *     requirements={
     *         "rule" = "\d+",
     *         "error_message" = "'limit' must be an integer greater than 0"
     *     },
     *     strict=true,
     *     nullable=true,
     *     description="(optional) Max number of products per page."
     * )
     * @Rest\QueryParam(
     *     name="page",
     *     requirements={
     *         "rule" = "\d+",
     *         "error_message" = "'page' must be an integer greater than 0"
     *     },
     *     strict=true,
     *     nullable=true,
     *     description="(optional) The requested paginated page."
     * )
     * @Rest\View
     * @SWG\Get(
     *     summary="Get a list of products",
     *     description="Retrieve a list of products.",
     *     operationId="list",
     *     produces={"application/json"},
     *     @SWG\Response(
     *         response=200,
     *         description="Returns a paginated list of products."
     *     ),
     *     @SWG\Response(
     *         response=403,
     *         description="OAuth2 authentication required."
     *     ),
     *     @SWG\Parameter(
     *         name="brand",
     *         in="query",
     *         type="string",
     *         description="(optional) The product's brand name to search for."
     *     ),
     *     @SWG\Parameter(
     *         name="limit",
     *         in="query",
     *         type="integer",
     *         description="(optional) The maximum number of products per page."
     *     ),
     *     @SWG\Parameter(
     *         name="page",
     *         in="query",
     *         type="integer",
     *         description="(optional) The requested paginated page."
     *     )
     * )
     * @SWG\Tag(name="Products")
     * @Security(name="Bearer")
     */
    public function list(ParamFetcherInterface $paramFetcher)
    {
        $pager = $this->getDoctrine()->getRepository(Product::class)->search(
            $paramFetcher->get('brand'),
            $paramFetcher->get('order'),
            $paramFetcher->get('limit'),
            $paramFetcher->get('page')
        );

        return $this->tools->setCache($this, 3600, new Products($pager));
    }

    // We use a custom ParamConverter to display a custom "not found" message:
    // App/ParamConverter/ProductParamConverter.php
    /**
     * @Rest\Get(
     *     path = "/products/{id}",
     *     name = "product_show",
     *     requirements = {"id"="\d+"}
     * )
     * @Rest\View
     * @SWG\Get(
     *     summary="Get a single product",
     *     description="Retrieve a single product.",
     *     operationId="show",
     *     produces={"application/json"},
     *     @SWG\Response(
     *         response=200,
     *         description="Returns a product's details."
     *     ),
     *     @SWG\Response(
     *         response=403,
     *         description="OAuth2 authentication required."
     *     ),
     *     @SWG\Response(
     *         response=404,
     *         description="Product not found."
     *     ),
     *     @SWG\Parameter(
     *         name="id",
     *         in="path",
     *         type="integer",
     *         description="The id of the product to be read."
     *     )
     * )
     * @SWG\Tag(name="Products")
     * @Security(name="Bearer")
     */
    public function show(Product $product)
    {
        return $this->tools->setCache($this, 3600, $product);
    }
}
