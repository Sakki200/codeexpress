<?php

namespace App\Controller;

use App\Entity\Subscription;
use App\Repository\OfferRepository;
use App\Repository\SubscriptionRepository;
use App\Repository\UserRepository;
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
        $user->setRoles(['ROLE_PREMIUM']);

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
    public function unsubscriptionToPremium(OfferRepository $ofr, EntityManagerInterface $em): Response
    {
        $user = $this->getUser();
        $ofr = $ofr->findOneByName('premium');

        $subscriptions = $em->getRepository(Subscription::class)->findBy([
            'author' => $user,
            'offer' => $ofr
        ]);

        if ($subscriptions) {
            foreach ($subscriptions as $sb) {
                $em->remove($sb);
                $user->removeRole(['ROLE_PREMIUM']);
                $em->flush();
            }
        }

        return $this->redirectToRoute('app_profile');
    }
}
