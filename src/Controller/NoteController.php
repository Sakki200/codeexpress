<?php

namespace App\Controller;

use App\Entity\Note;
use App\Form\NoteType;
use App\Repository\NoteRepository;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\String\Slugger\SluggerInterface;

#[Route('/notes')]
class NoteController extends AbstractController
{
    #[Route('/', name: 'app_note_all', methods: ['GET'])]
    public function all(NoteRepository $nr): Response
    {
        return $this->render('note/all.html.twig', [
            'allNotes' => $nr->findBy(['is_public' => true], ['created_at' => 'DESC'])
        ]);
    }
    #[Route('/n/{slug}', name: 'app_show', methods: ['GET'])]
    public function show(NoteRepository $nr, string $slug): Response
    {
        return $this->render('note/show.html.twig', [
            'note' => $nr->findOneBySlug($slug)
        ]);
    }
    #[Route('/u/{username}', name: 'app_note_user', methods: ['GET'])]
    public function userNotes(UserRepository $user, string $username): Response
    {
        $author = $user->findOneByUsername($username); // Recherche de l'utilisateur
        return $this->render('note/user_note.html.twig', [
            'author' => $author,
            'userNotes' => $user->getNotes($author) // Récupération des notes de l'utilisateur
        ]);
    }
    #[Route('/new', name: 'app_note_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $em, SluggerInterface $slugger): Response
    {
        $form = $this->createForm(NoteType::class); // Chargement du formulaire
        $form = $form->handleRequest($request); // Recuperation des données de la requête POST

        // Traitement des données
        if ($form->isSubmitted() && $form->isValid()) {
            $note = new Note();
            $note
                ->setTitle($form->get('title')->getData())
                ->setSlug($slugger->slug($note->getTitle()))
                ->setContent($form->get('content')->getData())
                ->setPublic($form->get('is_public')->getData())
                ->setCategory($form->get('category')->getData())
                ->setAuthor($form->get('author')->getData())
            ;
            $em->persist($note);
            $em->flush();

            $this->addFlash('success', 'Your note has been created');
            return $this->redirectToRoute('app_show', ['slug' => $note->getSlug()]);
        }
        return $this->render('note/new.html.twig', [
            'noteForm' => $form
        ]);
    }
    #[Route('/edit/{slug}', name: 'app_note_edit', methods: ['GET', 'POST'])]
    public function edit(NoteRepository $nr, string $slug): Response
    {
        $note = $nr->findOneBySlug($slug);
        return $this->render('note/edit.html.twig', []);
    }
    #[Route('/delete/{slug}', name: 'app_death_note', methods: ['POST'])]
    public function deathNote(NoteRepository $nr, string $slug): Response
    {
        $note = $nr->findOneBySlug($slug);
        $this->addFlash('success', 'Your code snippet has been deleted.');
        return $this->redirectToRoute('app_note_user');
    }
}
