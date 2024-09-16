<?php

namespace App\Controller;

use App\Repository\NoteRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
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
}
