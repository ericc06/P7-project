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
    private $tools;

    public function __construct(Tools $tools)
    {
        $this->tools = $tools;
    }

    public function load(ObjectManager $manager)
    {
        // Creation of 100 end users
        for ($i = 0; $i < 100; $i++) {
            $endUser = new EndUser();
            $endUser->setFirstName(self::getAFirstName());
            $endUser->setLastName(self::getALastName());
            $email = $endUser->getFirstName().'.'.$endUser->getLastName()
                .mt_rand(1, 99).'@mail'.$this->tools->getRandNumStr(3).'.loc';
            $endUser->setEmail($email);
            $endUser->setPhoneNumber($this->tools->getRandNumStr(10));
            $endUser->setCreationDate(new \DateTime());
            $endUser->setReseller(self::getAReseller());
            $manager->persist($endUser);
        }

        $manager->flush();
    }

    public function getAFirstName()
    {
        return $this->tools->getRandValFromArray([
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
        return $this->tools->getRandValFromArray([
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

    public function getAReseller()
    {
        return $this->tools->getRandValFromArray(
            [
                $this->getReference(ClientData::RESELLER_1),
                $this->getReference(ClientData::RESELLER_2),
                $this->getReference(ClientData::RESELLER_3)
            ]
        );
    }

    // Making this file load after ClientData fixtures
    public function getDependencies()
    {
        return [ClientData::class];
    }
}
