<?php
// src/DataFixtures/AppFixtures.php

namespace App\DataFixtures;

use App\Entity\Product;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\Persistence\ObjectManager;

class AppFixtures extends Fixture
{
    public function load(ObjectManager $manager)
    {
        // Creation of 10 products
        for ($i = 0; $i < 10; $i++) {
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

        $manager->flush();
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

    public function getRandValFromArray($array)
    {
        $key = array_rand($array);
        $value = $array[$key];
        return $value;
    }
}
