<?php

namespace App\Controller;

use App\Repository\NoteRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class NoteController extends AbstractController
{
    #[Route('/all', name: 'app_all')]
    public function notes(NoteRepository $notes): Response
    {
        return $this->render('note/all.html.twig', [
            'notes' => $notes->findAll()
        ]);
    }
    #[Route('/note/{slug}', name: 'app_show')]
    public function show(NoteRepository $notes, string $slug): Response
    {
        return $this->render('note/show.html.twig', [
            'note' => $notes->findBy(['slug' => $slug])
        ]);
    }
    #[Route('/my-notes', name: 'app_my_notes')]
    public function myNotes(): Response
    {
        return $this->render('note/note.html.twig', []);
    }
}
