<?php

namespace App\DataFixtures;

use Faker\Factory;
use App\Entity\View;
use Doctrine\Persistence\ObjectManager;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;

class ViewFixtures extends Fixture implements DependentFixtureInterface
{
    public function load(ObjectManager $manager): void
    {
        $faker = Factory::create('fr_FR');

        $noteCount = 45;

        for ($i = 0; $i < rand(100, 1000); $i++) {
            $view = new View();
            $view
                ->setNote($this->getReference('note_' . $faker->numberBetween(0, $noteCount - 1)))
                ->setIpAdress($faker->ipv4);
            $manager->persist($view);
        }
        $manager->flush();
    }

    public function getDependencies()
    {
        return [NoteFixtures::class];
    }
}
