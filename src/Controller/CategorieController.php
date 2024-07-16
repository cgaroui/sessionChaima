<?php

namespace App\Controller;

use App\Entity\Categorie;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class CategorieController extends AbstractController
{
    #[Route('/categorie', name: 'app_categorie')]
    public function index(EntityManagerInterface $entityManager): Response
    {
        $categories = $entityManager->getRepository(Categorie::class)->findAll();

        return $this->render('categorie/index.html.twig', [
            'categories' => $categories,
        ]);
    }

       //id ici la clé primaire de l'objet categorie qu'on veut recuperer 
       #[Route('/categorie/{id}', name: 'show_categorie')]
       public function show(Categorie $categorie): Response
       {
           return $this->render('categorie/show.html.twig', [
               'categorie' => $categorie
            ]);
       }
}
