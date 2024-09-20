<?php

namespace App\Controller;

use App\Entity\Like;
use App\Repository\LikeRepository;
use App\Repository\NoteRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

#[IsGranted('IS_AUTHENTICATED_FULLY')]
class LikeController extends AbstractController
{
    #[Route('/like', name: 'app_like', methods: ['POST'])]
    public function add(Request $request, LikeRepository $lr, NoteRepository $nr, EntityManagerInterface $em): Response
    {
        $referer = $request->headers->get('referer');
        $like = $lr->findOneByIdAndAuthor($request->request->get('note_id'), $this->getUser());
        $note = $nr->findOneById($request->request->get('note_id'));

        if (!$like) {
            $newLike = new Like();
            $newLike
                ->setNote($note)
                ->setAuthor($this->getUser());
            $em->persist($newLike);
            $em->flush();
        } elseif ($like->getAuthor() === $this->getUser()) {
            $em->remove($like);
            $em->flush();
        }

        return $referer ? $this->redirect($referer) : $this->redirectToRoute('app_note_all');
    }
}