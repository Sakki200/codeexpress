<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class HomeController extends AbstractController
{
    #[Route('/', name: 'app_home')]
    public function index(): Response
    {
        return $this->render('home/index.html.twig', [
            'controller_name' => 'Ceci est un nom !',
        ]);
    }
    #[Route('/profile', name: 'app_profile')]
    public function profile(): Response
    {
        return $this->render('home/profile.html.twig', [
            'controller_name' => 'Ceci est un nom !',
        ]);
    }
    #[Route('/notes', name: 'app_notes')]
    public function notes(): Response
    {
        return $this->render('home/notes.html.twig', [
            'controller_name' => 'Ceci est un nom !',
        ]);
    }
    #[Route('/notes?id=', name: 'app_note')]
    public function note(): Response
    {
        return $this->render('home/note.html.twig', [
            'controller_name' => 'Ceci est un nom !',
        ]);
    }
    #[Route('/mynotes', name: 'app_my_notes')]
    public function myNotes(): Response
    {
        return $this->render('home/my_notes.html.twig', [
            'controller_name' => 'Ceci est un nom !',
        ]);
    }
    #[Route('/categories', name: 'app_categories')]
    public function categories(): Response
    {
        return $this->render('home/categories.html.twig', [
            'controller_name' => 'Ceci est un nom !',
        ]);
    }
}
