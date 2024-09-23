<?php

namespace App\Controller;

use App\Repository\NoteRepository;
use App\Service\EmailNotificationService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class HomeController extends AbstractController
{
    #[Route('/', name: 'app_home', methods: ['GET'])]
    public function index(NoteRepository $nr): Response
    {
        //Si public oui, Trie par date de création, Limite 6
        $lastNotes = $nr->findBy(['is_public' => true], ['created_at' => 'DESC'], 6);
        $totalNotes = $nr->findBy(['is_public' => true]);

        return $this->render('home/index.html.twig', [
            'totalNotes' => count($totalNotes),
            'lastNotes' => $lastNotes
        ]);
    }
    #[Route('/email', name: 'app_email')]
    public function testEmail(EmailNotificationService $ems, Request $request): Response
    {
        $case = "registration";
        $ems->sendEmail($this->getUser()->getEmail(), $case);

        return new Response(" Email sent to  {$this->getUser()->getEmail()} <br>
        Choose a case : <br>
        <a href='/email?case=premium'>Premium</a> <br>
        <a href='/email?case=registration'>Registration</a>");
    }
}
