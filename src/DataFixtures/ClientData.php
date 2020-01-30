<?php

namespace App\DataFixtures;

use App\Entity\Client;
use App\Entity\Reseller;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\Persistence\ObjectManager;
use FOS\UserBundle\Doctrine\UserManager;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use App\Tools\Tools;

class ClientData extends Fixture implements ContainerAwareInterface
{
    private $tools;

    public const RESELLER_1 = 'reseller-1';
    public const RESELLER_2 = 'reseller-2';
    public const RESELLER_3 = 'reseller-3';

    const USER_MANAGER = 'fos_user.user_manager';

    public function __construct(Tools $tools)
    {
        $this->tools = $tools;
    }

    /**
     * @param ContainerInterface|null $container
     */
    public function setContainer(ContainerInterface $container = null)
    {
        $this->container = $container;
    }

    public function load(ObjectManager $manager)
    {
        // Creation of 3 OAuth clients with the related resellers (FOSUser users)
        for ($i = 1; $i < 4; $i++) {
            $oauth2Client = new Client();

            $oauth2Client->setId($i);
            $oauth2Client->setRandomId($this->tools->getRandAlphaNumStrLow(20));
            $oauth2Client->setRedirectUris([]);
            $oauth2Client->setSecret($this->tools->getRandAlphaNumStrLow(40));
            $oauth2Client->setAllowedGrantTypes(['password', 'refresh_token']);

            $reseller = self::createReseller($i, $oauth2Client);
            $oauth2Client->setReseller($reseller);

            // Reseller entity automatically persisted thanks to cascade={"persist"}
            $manager->persist($oauth2Client);

            // We create a reference to the current reseller in order to share it
            // with the EndUserData fixture.
            $constantName = "RESELLER_" . $i;
            $this->addReference(constant("self::{$constantName}"), $reseller);
        }

        $manager->flush();
    }

    public function createReseller($i, $client)
    {
        $userManager = $this->container->get(static::USER_MANAGER);
        /** @var Reseller $reseller */
        $reseller = $userManager->createUser();

        $reseller
            ->setShopName("my-shop-".$i)
            ->setEnabled(true)
            ->setRoles([Reseller::ROLE_USER])
            ->setUsername("myshop".$i)
            ->setPlainPassword("myshop".$i)
            ->setEmail("myshop".$i."@mail.tld")
            ->setClient($client)
        ;

        return $reseller;
    }
}
