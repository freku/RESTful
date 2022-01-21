<?php

namespace App\DataFixtures;

use App\Entity\Product;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class ProductFixture extends Fixture
{
    public function load(ObjectManager $manager): void
    {

        $product1 = new Product();
        $product1->setTitle("Chocolate");
        $product1->setPrice("1.99 USD");

        $product2 = new Product();
        $product2->setTitle("Chips");
        $product2->setPrice("2.99 USD");

        $product3 = new Product();
        $product3->setTitle("Beer");
        $product3->setPrice("3.99 USD");

        $product4 = new Product();
        $product4->setTitle("Pineapple");
        $product4->setPrice("4.99 USD");

        $product5 = new Product();
        $product5->setTitle("Car");
        $product5->setPrice("5675.99 USD");

        $manager->persist($product1);
        $manager->persist($product2);
        $manager->persist($product3);
        $manager->persist($product4);
        $manager->persist($product5);
        $manager->flush();
    }
}
