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
use Nelmio\ApiDocBundle\Annotation\Model;
use Nelmio\ApiDocBundle\Annotation\Security;
use Swagger\Annotations as SWG;
use App\Tools\Tools;
use Psr\Log\LoggerInterface;

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
     * @SWG\Response(
     *     response=200,
     *     description="Returns a paginated list of products.",
     *     @Model(type=Products::class)
     * )
     * @SWG\Response(
     *     response=403,
     *     description="OAuth2 authentication required."
     * )
     * @SWG\Parameter(
     *     name="brand",
     *     in="query",
     *     type="string",
     *     description="(optional) The product's brand name to search for."
     * )
     * @SWG\Parameter(
     *     name="limit",
     *     in="query",
     *     type="integer",
     *     description="(optional) The maximum number of products per page."
     * )
     * @SWG\Parameter(
     *     name="page",
     *     in="query",
     *     type="integer",
     *     description="(optional) The requested paginated page."
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

    // We use a custom ParamConverter:
    // App/ParamConverter/ProductParamConverter.php
    /**
     * @Rest\Get(
     *     path = "/products/{id}",
     *     name = "product_show",
     *     requirements = {"id"="\d+"}
     * )
     * @Rest\View
     * @SWG\Response(
     *     response=200,
     *     description="Returns a product's details.",
     *     @Model(type=Product::class)
     * )
     * @SWG\Response(
     *     response=403,
     *     description="OAuth2 authentication required."
     * )
     * @SWG\Response(
     *     response=404,
     *     description="Product not found."
     * )
     * @SWG\Parameter(
     *     name="id",
     *     in="path",
     *     type="integer",
     *     description="The id of the product to be read."
     * )
     * @SWG\Tag(name="Products")
     * @Security(name="Bearer")
     */
    public function show(Product $product)
    {
        return $this->tools->setCache($this, 3600, $product);
    }
}
