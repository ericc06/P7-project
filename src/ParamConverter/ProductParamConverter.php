<?php

namespace App\ParamConverter;

use App\Entity\Product;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Sensio\Bundle\FrameworkExtraBundle\Request\ParamConverter\ParamConverterInterface;
use Symfony\Component\HttpFoundation\Request;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\NoResultException;
use App\Exception\ResourceNotFoundException;

class ProductParamConverter implements ParamConverterInterface
{
    private $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    public function supports(ParamConverter $configuration)
    {
        // If the controller parameter name is different from 'product',
        // we don't use this converter
        if ('product' !== $configuration->getName()) {
            return false;
        }

        return true;
    }

    public function apply(Request $request, ParamConverter $configuration)
    {
        $product = $this->entityManager->getRepository(Product::class)
            ->find($request->attributes->get('id'));

        // If the given endUser id does not exist,
        // we throw a custon "not found" exception.
        if (null === $product) {
            $message = 'Product not found.';

            throw new ResourceNotFoundException($message);
        }

        // We update the attribute's new value.
        $request->attributes->set('product', $product);
    }
}
