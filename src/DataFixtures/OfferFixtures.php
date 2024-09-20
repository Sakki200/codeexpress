<?php

namespace App\DataFixtures;

use App\Entity\Offer;
use Doctrine\Persistence\ObjectManager;
use Doctrine\Bundle\FixturesBundle\Fixture;

class OfferFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        $offer = new Offer();
        $offer
            ->setName('premium')
            ->setPrice(4.99)
            ->setFeatures('Premium offer advantage : - You can use a gif avatar - And many more !')
        ;
        $manager->persist($offer);

        $manager->flush();
    }
}
