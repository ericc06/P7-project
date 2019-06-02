<?php
// src/DataFixtures/EndUserData.php

namespace App\DataFixtures;

use App\Entity\EndUser;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\Persistence\ObjectManager;
use App\Tools\Tools;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;

class EndUserData extends Fixture implements DependentFixtureInterface
{
    public function load(ObjectManager $manager)
    {
        // Creation of 100 end users
        for ($i = 0; $i < 100; $i++) {
            $endUser = new EndUser();
            $endUser->setFirstName(self::getAFirstName());
            $endUser->setLastName(self::getALastName());
            $email = $endUser->getFirstName().'.'.$endUser->getLastName()
                .mt_rand(1, 99).'@mail'.Tools::getRandNumStr(3).'.loc';
            $endUser->setEmail($email);
            $endUser->setPhoneNumber(Tools::getRandNumStr(10));
            $endUser->setCreationDate(new \DateTime());
            $endUser->setClient(self::getAClient());
            $manager->persist($endUser);
        }

        $manager->flush();
    }

    public function getAFirstName()
    {
        return Tools::getRandValFromArray([
            'André',
            'Alice',
            'Bernard',
            'Corinne',
            'François',
            'Henri',
            'Martine',
            'Rémi',
            'Sophie',
            'William'
        ]);
    }

    public function getALastName()
    {
        return Tools::getRandValFromArray([
            'Anduis',
            'Blain',
            'Daoud',
            'Filipini',
            'Leleu',
            'Martin',
            'Stone',
            'Truli',
            'Uzul',
            'Walter'
        ]);
    }

    public function getAClient()
    {
        return Tools::getRandValFromArray(
            [
                $this->getReference(ClientData::CLIENT_1),
                $this->getReference(ClientData::CLIENT_2),
                $this->getReference(ClientData::CLIENT_3)
            ]
        );
    }

    // Making this file load after ClientData fixtures
    public function getDependencies()
    {
        return array(
            ClientData::class,
        );
    }
}
