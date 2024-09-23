<?php

namespace App\Service;

use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;

class EmailNotificationService
{
    public function __construct(private MailerInterface $mailer)
    {
        $this->mailer = $mailer;
    }

    public function sendEmail(string $receiver, string $case): ?string
    {
        try {
            // $email = (new Email()) // Email sans template
            $email = (new TemplatedEmail()) // Avec template
                ->from('hello@codexpress.fr')
                ->to($receiver);
                //->cc('cc@example.com') //copy
                //->bcc('bcc@example.com') //bind copy?
                //->replyTo('fabien@example.com') //redirect vers la personne à répondre
                //->priority(Email::PRIORITY_HIGH)
                // ->subject('Time for Symfony Mailer!') // Entête
                // ->text('Sending emails is fun again!') //Le mode text sans les balises HTML
                // ->html('<p>See Twig integration for better HTML integration!</p>')
                // ->htmlTemplate('email/premium.html.twig')
                
                if ($case === 'premium') {
                    $email
                    ->priority(Email::PRIORITY_HIGH)
                    ->subject('Thanks for the purchase !')
                    ->htmlTemplate('email/premium.html.twig');
                    
                } elseif ($case === 'registration') {
                    
                    $email
                    ->subject('Welcome to CodeXpress !')
                    ->htmlTemplate('email/welcome.html.twig');
            }

            $this->mailer->send($email);
            return "Email sent successfully";
        } catch (\Exception $e) {
            return "Error sending email";
        }
    }
}
