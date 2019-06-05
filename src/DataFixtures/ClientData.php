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

    public const CLIENT_1 = 'client-1';
    public const CLIENT_2 = 'client-2';
    public const CLIENT_3 = 'client-3';

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

            $oauth2Client->setReseller(self::createReseller($i, $oauth2Client, $manager));

            $manager->persist($oauth2Client);

            /** @var ClassMetadata $metadata */
            /*
            $metadata = $manager->getClassMetadata(get_class($oauth2Client));

            $metadata->setIdGeneratorType(ClassMetadata::GENERATOR_TYPE_NONE);
            $metadata->setIdGenerator(new AssignedGenerator());
            */
            $constantName = "CLIENT_" . $i;
            $this->addReference(constant("self::{$constantName}"), $oauth2Client);
        }

        $manager->flush();
    }

    public function createReseller($i, $client, $manager)
    {
        $userManager = $this->container->get(static::USER_MANAGER);
        /** @var Reseller $reseller */
        $reseller = $userManager->createUser();

        $reseller
            ->setShopName("my-shop-".$i)
            ->setEnabled(true)
            ->setRoles([Reseller::ROLE_SUPER_ADMIN])
            ->setUsername("myshop".$i)
            ->setPlainPassword("myshop".$i)
            ->setEmail("myshop".$i."@mail.tld")
            ->setClient($client)
        ;
        $manager->persist($reseller);
    }
}
