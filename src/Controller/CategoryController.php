<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class CategoryController extends AbstractController
{
    #[Route('/categories', name: 'app_categories')]
    public function categories(): Response
    {
        return $this->render('category/categories.html.twig', []);
    }
    #[Route('/category/{title}', name: 'app_category')]
    public function category(): Response
    {
        return $this->render('category/category.html.twig', []);
    }
}
