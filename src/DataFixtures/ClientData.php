<?php

namespace App\DataFixtures;

use App\Entity\Client;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\Persistence\ObjectManager;
use Doctrine\ORM\Id\AssignedGenerator;
use Doctrine\ORM\Mapping\ClassMetadata;
use App\Tools\Tools;

class ClientData extends Fixture
{
    private $tools;

    public const CLIENT_1 = 'client-1';
    public const CLIENT_2 = 'client-2';
    public const CLIENT_3 = 'client-3';

    public function __construct(Tools $tools)
    {
        $this->tools = $tools;
    }

    public function load(ObjectManager $manager)
    {
        // Creation of 3 OAuth clients
        for ($i = 1; $i < 4; $i++) {
            $oauth2Client = new Client();

            $oauth2Client->setId($i);
            $oauth2Client->setRandomId($this->tools->getRandAlphaNumStrLow(20));
            $oauth2Client->setRedirectUris([]);
            $oauth2Client->setSecret($this->tools->getRandAlphaNumStrLow(40));
            $oauth2Client->setAllowedGrantTypes(['password', 'refresh_token']);

            $manager->persist($oauth2Client);

            /** @var ClassMetadata $metadata */
            $metadata = $manager->getClassMetadata(get_class($oauth2Client));

            $metadata->setIdGeneratorType(ClassMetadata::GENERATOR_TYPE_NONE);
            $metadata->setIdGenerator(new AssignedGenerator());

            $constantName = "CLIENT_" . $i;
            $this->addReference(constant("self::{$constantName}"), $oauth2Client);
        }

        $manager->flush();
    }
}
