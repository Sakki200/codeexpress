<?php

namespace App\Controller;

use App\Repository\NoteRepository;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class SearchController extends AbstractController
{
    #[Route('/search', name: 'app_search', methods: ['GET'])]
    public function search(Request $request, NoteRepository $nr, PaginatorInterface $paginator): Response
    {
        // SI l'accès à la page se fait sans le formulaire on redirige sans paramètres
        if ($request->get('q') === null) {
            return $this->render('search/results.html.twig');
        }

        $pagination = $paginator->paginate(
            $nr->findByQuery($request->get('q')), /* la requête */
            $request->query->getInt('page', 1), /*page en cours*/
            24 /*élements par page*/
        );
        return $this->render('search/results.html.twig', ['allNotes' => $pagination, 'searchQuery' => $request->get('q')]);
    }
}
