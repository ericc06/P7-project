<?php
// src/DataFixtures/AppFixtures.php

namespace App\DataFixtures;

use App\Entity\Product;
use App\Entity\EndUser;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\Persistence\ObjectManager;

class AppFixtures extends Fixture
{
    public function load(ObjectManager $manager)
    {
        self::createProducts($manager);
        self::createEndUsers($manager);

        $manager->flush();
    }

    public function createProducts(ObjectManager $manager)
    {
        // Creation of 10 products
        for ($i = 0; $i < 20; $i++) {
            $product = new Product();
            $product->setReference(self::getRandomString());
            $product->setBrand('Brand'.mt_rand(1, 3));
            $product->setModel('Model'.$i);
            $product->setMemCapacity(self::getRandValFromArray([16, 32, 64]));
            $product->setScreenSize(self::getRandValFromArray([5.4, 5.9, 6.4]));
            $product->setColor(self::getRandValFromArray(['Black', 'Silver', 'Blue']));
            $product->setPrice(mt_rand(40, 120) * 10);
            $product->setStock(mt_rand(5, 50));
            $product->setCreationDate(new \DateTime());
            $manager->persist($product);
        }
    }

    public function createEndUsers(ObjectManager $manager)
    {
        // Creation of 100 end users
        for ($i = 0; $i < 100; $i++) {
            $endUser = new EndUser();
            $endUser->setFirstName(self::getRandValFromArray([
                'André',
                'Alice',
                'Bernard',
                'Corinne',
                'François',
                'Henri',
                'Martine',
                'Rémi',
                'Sophie',
                'William',
            ]));
            $endUser->setLastName(self::getRandValFromArray([
                'Anduis',
                'Blain',
                'Daoud',
                'Filipini',
                'Leleu',
                'Martin',
                'Stone',
                'Truli',
                'Uzul',
                'Walter',
            ]));
            $email = $endUser->getFirstName().'.'.$endUser->getLastName()
                .mt_rand(1, 999).'@'.self::getRandValFromArray(['gmail', 'test', 'yahoo']).'.loc';
            $endUser->setEmail($email);
            $endUser->setPhoneNumber(self::getRandomNumString(10));
            $endUser->setCreationDate(new \DateTime());
            $manager->persist($endUser);
        }
    }

    public function getRandomString($len = 10)
    {
        $chars = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $charsLen = strlen($chars);
        $randomString = '';
        for ($i = 0; $i < $len; $i++) {
            $randomString .= $chars[rand(0, $charsLen - 1)];
        }
        return $randomString;
    }

    public function getRandomNumString($len = 10)
    {
        $chars = '0123456789';
        $charsLen = strlen($chars);
        $randomString = '';
        for ($i = 0; $i < $len; $i++) {
            $randomString .= $chars[rand(0, $charsLen - 1)];
        }
        return $randomString;
    }

    public function getRandValFromArray($array)
    {
        $key = array_rand($array);
        $value = $array[$key];
        return $value;
    }
}
