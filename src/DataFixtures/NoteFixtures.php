<?php

namespace App\DataFixtures;

use Faker\Factory;
use App\Entity\Note;
use App\Entity\Category;
use Doctrine\Persistence\ObjectManager;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Symfony\Component\String\Slugger\SluggerInterface;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;

class NoteFixtures extends Fixture implements DependentFixtureInterface
{
    public function __construct(private SluggerInterface $slugger) {}

    public function load(ObjectManager $manager): void
    {
        $faker = Factory::create('fr_FR');
        
        $categories = $manager->getRepository(Category::class)->findAll();

        if (empty($categories)) {
            throw new \RuntimeException('No categories found. Make sure CategoryFixtures are loaded first.');
        }

        $users = [];
        for ($i = 0; $i < 5; $i++) {
            $users[] = $this->getReference('user_' . $i);
        }
        $users[] = $this->getReference('user_admin');

        $noteCount = 0;
        foreach ($users as $user) {
            for ($i = 0; $i < 5; $i++) {
                $note = new Note();
                $note
                    ->setTitle($faker->words(5, true))
                    ->setSlug($this->slugger->slug($note->getTitle()))
                    ->setContent($faker->randomHtml)
                    ->setPublic($faker->boolean(70))
                    ->setAuthor($user)
                    ->setCategory($faker->randomElement($categories));
                $manager->persist($note);
                
                // Add a reference to this note
                $this->addReference('note_' . $noteCount, $note);
                $noteCount++;
            }
        }

        $manager->flush();
    }

    public function getDependencies()
    {
        return [UserFixtures::class, CategoryFixtures::class];
    }
}