<?php

namespace App\DataFixtures;


use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use App\Entity\Category;
use App\Entity\Like;
use App\Entity\Network;
use App\Entity\Note;
use App\Entity\Offer;
use App\Entity\Subscription;
use App\Entity\User;
use Faker\Factory;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\String\Slugger\SluggerInterface;

class AppFixtures extends Fixture
{
    private $slug = null;
    private $hash = null;

    public function __construct(private SluggerInterface $slugger, private UserPasswordHasherInterface $hasher)
    {
        $this->slug = $slugger;
        $this->hash = $hasher;
    }
    public function load(ObjectManager $manager): void
    {
        // :: veut dire qu'on appel la fonction create de la class Factory sans faire de new Factory
        $faker = Factory::create('fr_FR');

        # Tableau contenant les catégories
        $categories = [
            'HTML' => 'https://cdn.jsdelivr.net/gh/devicons/devicon@latest/icons/html5/html5-plain.svg',
            'CSS' => 'https://cdn.jsdelivr.net/gh/devicons/devicon@latest/icons/css3/css3-plain.svg',
            'JavaScript' => 'https://cdn.jsdelivr.net/gh/devicons/devicon@latest/icons/javascript/javascript-plain.svg',
            'PHP' => 'https://cdn.jsdelivr.net/gh/devicons/devicon@latest/icons/php/php-plain.svg',
            'SQL' => 'https://cdn.jsdelivr.net/gh/devicons/devicon@latest/icons/postgresql/postgresql-plain.svg',
            'JSON' => 'https://cdn.jsdelivr.net/gh/devicons/devicon@latest/icons/json/json-plain.svg',
            'Python' => 'https://cdn.jsdelivr.net/gh/devicons/devicon@latest/icons/python/python-plain.svg',
            'Ruby' => 'https://cdn.jsdelivr.net/gh/devicons/devicon@latest/icons/ruby/ruby-plain.svg',
            'C++' => 'https://cdn.jsdelivr.net/gh/devicons/devicon@latest/icons/cplusplus/cplusplus-plain.svg',
            'Go' => 'https://cdn.jsdelivr.net/gh/devicons/devicon@latest/icons/go/go-wordmark.svg',
            'bash' => 'https://cdn.jsdelivr.net/gh/devicons/devicon@latest/icons/bash/bash-plain.svg',
            'Markdown' => 'https://cdn.jsdelivr.net/gh/devicons/devicon@latest/icons/markdown/markdown-original.svg',
            'Java' => 'https://cdn.jsdelivr.net/gh/devicons/devicon@latest/icons/java/java-original-wordmark.svg',
        ];

        $networkArray = [
            'twitter' => 'https://twitter.com/',
            'facebook' => 'https://www.facebook.com/',
            'instagram' => 'https://www.instagram.com/',
            'youtube' => 'https://www.youtube.com/',
            'pinterest' => 'https://www.pinterest.com/',
            'linkedin' => 'https://www.linkedin.com/in/',
            'snapchat' => 'https://www.snapchat.com/',
            'reddit' => 'https://www.reddit.com/'
        ];
        $categoryArray = []; // Ce tableau nous servira pur conserver les objects Category
        $noteArray = []; // Ce tableau nous servira pur conserver les objects Note
        $userArray = []; // Ce tableau nous servira pur conserver les objects User

        //Pour l'offre premium
        $premiumOffer = new Offer;
        $premiumOffer
            ->setName('premium')
            ->setPrice(4.99)
            ->setFeatures('Premium offer advantage : - You can use a gif avatar - And many more !')
        ;
        $manager->persist($premiumOffer);


        foreach ($categories as $title => $icon) {
            $category = new Category();  // NOUVEL OBJET CATEGORY
            $category->setTitle($title); // AJOUT DU TITRE
            $category->setIcon($icon);   // AJOUT DE L'ICONE

            array_push($categoryArray, $category);
            $manager->persist($category); // AJOUT A LA BDD
        }

        for ($i = 0; $i < 10; $i++) {
            $username = $faker->userName; // GENERATION D'UN USERNAME RANDOM
            $emailUsername = $this->slug->slug($username); // USERNAME SLUGGYFIÉ
            $user = new User();
            $user
                ->setEmail($emailUsername . '@' . $faker->freeEmailDomain()) // freeEmailDomain() créer un nom de domain de mail e.g "gmail.com"
                ->setUsername($username)
                ->setPassword($this->hash->hashPassword($user, 'admin')) // HASH le mdp "admin"
                ->setRoles(['ROLE_USER'])
                ->setImage("https://avatar.iran.liara.run/public/" . $faker->randomNumber(2, true));

            array_push($userArray, $user);
            $manager->persist($user);

            //Subscription
            if ($faker->boolean(40) === true) {
                $subscription = new Subscription();
                $subscription
                    ->setAuthor($user)
                    ->setOffer($premiumOffer)
                    ->setStartDate($faker->dateTimeThisYear())
                    ->setEndDate($faker->dateTimeThisYear('+1 months'));

                $manager->persist($subscription);
            }

            //Note
            for ($j = 0; $j < 3; $j++) {

                $note = new Note();
                $note
                    ->setTitle($faker->sentence())
                    ->setSlug($this->slug->slug($note->getTitle()))
                    ->setContent($faker->randomHtml())
                    ->setPublic($faker->boolean(50))
                    ->setViews($faker->numberBetween(100, 10000))
                    ->setAuthor($user)
                    ->setCategory($faker->randomElement($categoryArray));

                array_push($noteArray, $note);
                $manager->persist($note);
            }
            //Network
            for ($k = 0; $k < 3; $k++) {
                $network = new Network();
                $randomKey = array_rand($networkArray);

                $network
                    ->setName($randomKey)
                    ->setAuthor($user)
                    ->setUrl($networkArray[$randomKey] . $username);

                $manager->persist($network);
            }
            //Likes
            for ($l = 0; $l < 500; $l++) {
                $like = new Like();
                $like
                    ->setAuthor($faker->randomElement($userArray))
                    ->setNote($faker->randomElement($noteArray));

                $manager->persist($like);
            }
        }

        $manager->flush($category);
    }
}
