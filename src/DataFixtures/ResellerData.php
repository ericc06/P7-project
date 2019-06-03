<?php

namespace App\DataFixtures;

use App\Entity\Reseller;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\Persistence\ObjectManager;
use FOS\UserBundle\Doctrine\UserManager;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

class ResellerData extends Fixture implements ContainerAwareInterface
{
    const USER_MANAGER = 'fos_user.user_manager';

    /**
     * @var ContainerInterface
     */
    private $container;

    /**
     * @var UserManager
     */
    private $userManager;

    /**
     * @param ContainerInterface|null $container
     */
    public function setContainer(ContainerInterface $container = null)
    {
        $this->container = $container;
        $this->userManager = $this->container->get(static::USER_MANAGER);
    }

    public function load(ObjectManager $manager)
    {
        /** @var Reseller $reseller */
        $reseller = $this->userManager->createUser();

        $reseller
            ->setShopName("my-shop")
            ->setEnabled(true)
            ->setRoles([Reseller::ROLE_SUPER_ADMIN])
            ->setUsername("myshop")
            ->setPlainPassword("myshop")
            ->setEmail("myshop@mail.tld")
        ;
        $manager->persist($reseller);
        $manager->flush();
    }
}
