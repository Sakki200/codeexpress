<?php

namespace App\Controller;

use App\Entity\Subscription;
use App\Repository\OfferRepository;
use App\Service\EmailNotificationService;
use App\Service\PaymentService;
use Doctrine\ORM\EntityManagerInterface;
use Stripe\Webhook;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
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
    #[Route('/s/checkout', name: 'app_subscription_checkout', methods: ['GET'])]
    public function checkout(PaymentService $ps): RedirectResponse
    {
        return $this->redirect($ps->askCheckout()->url);
    }

    // Route lorsque le paiement a réussi
    #[Route('/s/payment-success', name: 'app_payment_success')]
    public function paymentSuccess(Request $request, PaymentService $ps, EmailNotificationService $ens): Response
    {;
        if ($request->headers->get('referer') === 'https://checkout.stripe.com/') {
            $subscription = $ps->addSubscription();
            $ens->sendEmail(
                $this->getUser()->getEmail(),
                [
                    'subject' => 'Thank you for your purchase!',
                    'template' => 'premium',
                ]
            );
            return $this->render('subscription/payment-success.html.twig', [
                'subscription' => $subscription,
            ]);
        } else {
            $this->addFlash('error', "You can't take a subscription without a payment");
            return $this->redirectToRoute('app_subscription');
        }
        $this->addFlash('success', "Thanks for your subscription !");
        return $this->redirectToRoute('app_subscription');
    }

    // Route lorsque le paiement a échoué
    #[Route('/s/payment-cancel', name: 'app_payment_cancel')]
    public function paymentCancel(Request $request): Response
    {
        if ($request->headers->get('referer') === 'https://checkout.stripe.com/') {
            return $this->render('subscription/payment-cancel.html.twig');
        } else {
            $this->addFlash('error', "You can't take a subscription without a payment");
            return $this->redirectToRoute('app_subscription');
        }
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

    #[Route('/s/payment-webhook', name: 'app_stripe_webhook', methods: ['GET', 'POST'])]
    public function stripeWebhook(Request $request): Response
    {
        $payload = $request->getContent();
        $sigHeader = $request->headers->get('Stripe-Signature');
        $endpointSecret = $this->getParameter('stripe_webhook_secret');

        try {
            $event = Webhook::constructEvent(
                $payload,
                $sigHeader,
                $endpointSecret
            );
            dd($event);
        } catch (\UnexpectedValueException $e) {
            return new Response('Invalid payload', 400);
        } catch (\Stripe\Exception\SignatureVerificationException $e) {
            return new Response('Invalid signature', 400);
        }
    }
}
