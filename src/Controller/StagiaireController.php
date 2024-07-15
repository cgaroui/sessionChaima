<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class StagiaireController extends AbstractController
{
    #[Route('/Stagiaire', name: 'app_Stagiaire')]
    public function index(): Response
    {
        return $this->render('Stagiaire/index.html.twig', [
            'controller_name' => 'StagiaireController',
        ]);
    }
}
