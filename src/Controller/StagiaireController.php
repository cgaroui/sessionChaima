<?php

namespace App\Controller;

use App\Entity\Stagiaire;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class StagiaireController extends AbstractController
{
    #[Route('/stagiaire', name: 'app_stagiaire')]
    public function index(EntityManagerInterface $entityManager): Response
    {
        $stagiaires = $entityManager->getRepository(Stagiaire::class)->findAll();

        return $this->render('stagiaire/index.html.twig', [
            'stagiaires' => $stagiaires,
        ]);
    }

    #[Route('/stagiaire/{id}', name: 'show_stagiaire')]
    public function show(Stagiaire $stagiaire = null): Response
    {
        //je verifie si stagiaire existe alors j'affiche le detail 
        if ($stagiaire){
            return $this->render('stagiaire/show.html.twig', [
                'stagiaire' => $stagiaire,
            ]);

        }else{
            //sinon je regirige vers liste des stagiaires (pour sécuriser danns le cas ou l'on entre des identifiant dans l'url)
            return $this-> redirectToRoute("app_stagiaire");
        }
    }


}
