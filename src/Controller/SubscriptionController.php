<?php

namespace App\Controller;

use App\Entity\Subscription;
use App\Repository\OfferRepository;
use App\Repository\SubscriptionRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[IsGranted('IS_AUTHENTICATED_FULLY')]
class SubscriptionController extends AbstractController
{
    #[Route('/s', name: 'app_subscription', methods: ['GET'])]
    public function subscription(OfferRepository $ofr): Response
    {;

        return $this->render('subscription/subscription.html.twig', ['premium' => $ofr->findOneByName('premium')]);
    }
    #[Route('/s/premium', name: 'app_subscription_premium', methods: ['GET', 'POST'])]
    public function subscriptionToPremium(OfferRepository $ofr, EntityManagerInterface $em): Response
    {
        $currentDate = new \DateTime();

        $user = $this->getUser();
        $user->setPremium(true);

        $sb = new Subscription;
        $sb
            ->setAuthor($this->getUser())
            ->setOffer($ofr->findOneByName('premium'))
            ->setStartDate($currentDate)
            ->setEndDate((clone $currentDate)->modify('+1 month'));

        $em->persist($sb);
        $em->flush();

        return $this->redirectToRoute('app_profile');
    }
    #[Route('/s/unpremium', name: 'app_subscription_unpremium', methods: ['GET', 'POST'])]
    public function unsubscriptionToPremium(EntityManagerInterface $em): Response
    {
        $user = $this->getUser();

        $subscriptions = $em->getRepository(Subscription::class)->findBy([
            'author' => $user,
            'offer' => 3
        ]);

        if ($subscriptions) {
            foreach ($subscriptions as $sb) {
                $em->remove($sb);
                $user->setPremium(false);
                $em->flush();
            }
        }

        return $this->redirectToRoute('app_profile');
    }
}
