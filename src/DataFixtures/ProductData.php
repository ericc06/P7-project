<?php
// src/DataFixtures/ProductData.php

namespace App\DataFixtures;

use App\Entity\Product;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\Persistence\ObjectManager;
use App\Tools\Tools;

class ProductData extends Fixture
{
    private $tools;

    public function __construct(Tools $tools)
    {
        $this->tools = $tools;
    }

    public function load(ObjectManager $manager)
    {
        // Creation of 10 products
        for ($i = 0; $i < 20; $i++) {
            $product = new Product();
            $product->setReference($this->tools->getRandAlphaNumStr());
            $product->setBrand('Brand'.mt_rand(1, 3));
            $product->setModel('Model'.$i);
            $product->setMemCapacity($this->tools->getRandValFromArray([16, 32, 64]));
            $product->setScreenSize($this->tools->getRandValFromArray([5.4, 5.9, 6.4]));
            $product->setColor($this->tools->getRandValFromArray(['Black', 'Silver', 'Blue']));
            $product->setPrice(mt_rand(40, 120) * 1000);
            $product->setStock(mt_rand(5, 50));
            $product->setCreationDate(new \DateTime());
            $manager->persist($product);
        }

        $manager->flush();
    }
}
