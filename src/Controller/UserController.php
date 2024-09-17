<?php

namespace App\Controller;

use App\Entity\User;
use App\Repository\NoteRepository;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[IsGranted('IS_AUTHENTICATED_FULLY')]
class UserController extends AbstractController
{
    #[Route('/profile', name: 'app_profile', methods: ['GET'])]
    public function profile(NoteRepository $nr): Response
    {
        return $this->render(
            'user/profile.html.twig',
            ['userNotes' => $nr->FindByAuthor(['author', $this->getUser()], ['created_at' => 'DESC'])]
        );
    }
    #[Route('/profile/edit', name: 'app_profile_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, EntityManagerInterface $em): Response
    {
        $user = $this->getUser(); //Récupération de l'user authentifié
        return $this->render('user/edit.html.twig', []);
    }
}
