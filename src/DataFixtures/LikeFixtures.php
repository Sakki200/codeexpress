<?php

namespace App\DataFixtures;

use Faker\Factory;
use App\Entity\Like;
use Doctrine\Persistence\ObjectManager;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;

class LikeFixtures extends Fixture implements DependentFixtureInterface
{
    public function load(ObjectManager $manager): void
    {
        $faker = Factory::create('fr_FR');

        $regularUserCount = 5;
        $premiumUserCount = 3;
        $adminCount = 1;
        $totalUserCount = $regularUserCount + $premiumUserCount + $adminCount;

        $noteCount = 45; // 30 regular notes + 15 premium notes

        $userReferences = [];
        
        // Collect all user references
        for ($i = 0; $i < $regularUserCount; $i++) {
            $userReferences[] = 'user_' . $i;
        }
        for ($i = 0; $i < $premiumUserCount; $i++) {
            $userReferences[] = 'premium_user_' . $i;
        }
        $userReferences[] = 'user_admin';

        for ($i = 0; $i < 100; $i++) {
            $like = new Like();
            $like
                ->setNote($this->getReference('note_' . $faker->numberBetween(0, $noteCount - 1)))
                ->setAuthor($this->getReference($faker->randomElement($userReferences)));
            $manager->persist($like);
        }

        $manager->flush();
    }

    public function getDependencies()
    {
        return [NoteFixtures::class, UserFixtures::class];
    }
}
