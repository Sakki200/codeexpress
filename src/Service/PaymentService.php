<?php

namespace App\Service;

use Stripe\Stripe;
use App\Entity\Subscription;
use Stripe\Checkout\Session;
use App\Service\AbstractService;
use App\Repository\OfferRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;

class PaymentService extends AbstractService
{
    /**
     * Propriétés du service
     * - Offre sélectionnée
     * - Nom de domaine
     * - Clé API Stripe
     * - Utilisateur courant
     */
    private $offer, $domain, $apiKey, $user;

    public function __construct(
        protected ParameterBagInterface $parameter,
        private OfferRepository $or,
        private readonly Security $security,
        private EntityManagerInterface $em
    ) {
        $this->parameter = $parameter;
        $this->offer = $or->findOneByName('Premium');
        $this->apiKey = $this->parameter->get('STRIPE_API_SK');
        $this->domain = 'https://127.0.0.1:8000/en';
        $this->user = $security->getUser();
    }


    public function askCheckout(): ?Session
    {
        Stripe::setApiKey($this->parameter->get('STRIPE_API_SK')); //Établissement de la connection à l'API
        header('Content-Type: application/json'); //Définition du type de contenu de la requête

        $checkoutSession = Session::create([
            'customer_email' => $this->user->getEmail(),
            'line_items' => [[
                'price_data' => [
                    'currency' => 'eur',
                    'unit_amount' => $this->offer->getPrice() * 100, // Stripe utilise des centimes
                    'product_data' => [ // Les informations du produit sont personnalisables
                        'name' => $this->offer->getName(),
                    ],
                ],
                'quantity' => 1,
            ]],
            'mode' => 'payment',
            'success_url' => $this->domain . '/s/payment-success',
            'cancel_url' => $this->domain . '/s/payment-cancel',
            'automatic_tax' => [
                'enabled' => false,
            ],
        ]);

        return $checkoutSession;
    }

    public function addSubscription(): ?Subscription
    {
        $subscription = new Subscription();
        $subscription
            ->setAuthor($this->user)
            ->setOffer($this->offer)
            ->setStartDate(new \DateTimeImmutable())
            ->setEndDate(new \DateTimeImmutable('+30 days'))
        ;
        $this->em->persist($subscription);
        $this->em->flush();

        $this->user->setRoles(['ROLE_PREMIUM']);
        $this->em->persist($this->user);
        $this->em->flush();

        return $subscription;
    }
}
