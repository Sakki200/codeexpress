<?php

namespace App\DataFixtures;

use Faker\Factory;
use App\Entity\User;
use App\Entity\Network;
use Doctrine\Persistence\ObjectManager;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class UserFixtures extends Fixture
{
    public function __construct(private UserPasswordHasherInterface $hasher) {}

    public function load(ObjectManager $manager): void
    {
        $faker = Factory::create('fr_FR');

        $networks = ['github', 'twitter', 'linkedin', 'facebook', 'reddit', 'instagram', 'youtube'];

        // Create admin user
        $admin = new User();
        $admin
            ->setEmail('hello@codexpress.fr')
            ->setUsername('admin')
            ->setPassword($this->hasher->hashPassword($admin, 'admin'))
            ->setRoles(['ROLE_ADMIN']);
        $manager->persist($admin);
        $this->addReference('user_admin', $admin);

        for ($i = 0; $i < 3; $i++) {
            $network = new Network();
            $network
                ->setName($faker->randomElement($networks))
                ->setUrl(
                    'https://' . 
                    strtolower($network->getName()) . 
                    '.com/' . 
                    $admin->getUsername()
                )
                ->setAuthor($admin);
            $manager->persist($network);
        }

        // Create regular users
        for ($i = 0; $i < 5; $i++) {
            $user = new User();
            $user
                ->setEmail($faker->email)
                ->setUsername($faker->userName)
                ->setPassword($this->hasher->hashPassword($user, 'userpassword'))
                ->setRoles(['ROLE_USER']);
            $manager->persist($user);
            $this->addReference('user_' . $i, $user);

            for ($j = 0; $j < 3; $j++) {
                $network = new Network();
                $network
                    ->setName($faker->randomElement($networks))
                    ->setUrl(
                        'https://' . 
                        strtolower($network->getName()) . 
                        '.com/' . 
                        $user->getUsername()
                    )
                    ->setAuthor($user);
                $manager->persist($network);
            }
        }

        // Create premium users
        for ($i = 0; $i < 3; $i++) {
            $premiumUser = new User();
            $premiumUser
                ->setEmail($faker->email)
                ->setUsername($faker->userName)
                ->setPassword($this->hasher->hashPassword($premiumUser, 'premiumpassword'))
                ->setRoles(['ROLE_PREMIUM']);
            $manager->persist($premiumUser);
            $this->addReference('premium_user_' . $i, $premiumUser);

            for ($k = 0; $k < 3; $k++) {
                $network = new Network();
                $network
                    ->setName($faker->randomElement($networks))
                    ->setUrl(
                        'https://' . 
                        strtolower($network->getName()) . 
                        '.com/' . 
                        $premiumUser->getUsername()
                    )
                    ->setAuthor($premiumUser);
                $manager->persist($network);
            }
        }

        $manager->flush();
    }
}