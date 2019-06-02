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
    public const CLIENT_1 = 'client-1';
    public const CLIENT_2 = 'client-2';
    public const CLIENT_3 = 'client-3';

    public function load(ObjectManager $manager)
    {
        // Creation of 3 OAuth clients
        for ($i = 1; $i < 4; $i++) {
            $oauth2Client = new Client();

            $oauth2Client->setId($i);
            $oauth2Client->setRandomId(Tools::getRandAlphaNumStrLow(20));
            $oauth2Client->setRedirectUris(array());
            $oauth2Client->setSecret(Tools::getRandAlphaNumStrLow(40));
            $oauth2Client->setAllowedGrantTypes(array('password', 'refresh_token'));

            $manager->persist($oauth2Client);

            /** @var ClassMetadata $metadata */
            $metadata = $manager->getClassMetadata(get_class($oauth2Client));

            $metadata->setIdGeneratorType(ClassMetadata::GENERATOR_TYPE_NONE);
            $metadata->setIdGenerator(new AssignedGenerator());
            
            self::letAddReference($i, $oauth2Client);
        }

        $manager->flush();
    }

    private function letAddReference($i, $oauth2Client)
    {
        switch ($i) {
            case 1:
                $this->addReference(self::CLIENT_1, $oauth2Client);
                break;
            case 2:
                $this->addReference(self::CLIENT_2, $oauth2Client);
                break;
            case 3:
                $this->addReference(self::CLIENT_3, $oauth2Client);
                break;
            default:
                break;
        }
    }
}
