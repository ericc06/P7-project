<?php

namespace App\ParamConverter;

use App\Entity\EndUser;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Sensio\Bundle\FrameworkExtraBundle\Request\ParamConverter\ParamConverterInterface;
use Symfony\Component\HttpFoundation\Request;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\NoResultException;
use App\Exception\ResourceNotFoundException;

class EndUserParamConverter implements ParamConverterInterface
{
    private $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    public function supports(ParamConverter $configuration)
    {
        // If the controller parameter name is different from 'endUser',
        // we don't use this converter
        if ('endUser' !== $configuration->getName()) {
            return false;
        }

        return true;
    }

    public function apply(Request $request, ParamConverter $configuration)
    {
        $endUser = $this->entityManager->getRepository(EndUser::class)
            ->find($request->attributes->get('id'));

        // If the given endUser id does not exist,
        // we throw a custon "not found" exception.
        if (null === $endUser) {
            $message = 'User not found.';

            throw new ResourceNotFoundException($message);
        }

        // We update the attribute's new value.
        $request->attributes->set('endUser', $endUser);
    }
}
