<?php

namespace App\Controller;

use App\Entity\Formation;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class FormationController extends AbstractController
{
    #[Route('/formations', name: 'app_formation')]
    public function index(EntityManagerInterface $entityManager): Response
    {
        $formations = $entityManager->getRepository(Formation::class)->findAll();

        return $this->render('formation/index.html.twig', [
            'formations' => $formations,
        ]);
    }

    #[Route('/formation/{id}', name: 'show_formation')]
    public function show(Formation $formation): Response
    {
        return $this->render('formation/show.html.twig', [
            'formation' => $formation,
        ]);
    }

    #[Route('/formation/{id}/sessions', name: 'formation_sessions')]
    public function sessions(Formation $formation): Response
    {
        // Supposez que $formation->getSessions() retourne la liste des sessions liées à cette formation
        $sessions = $formation->getSessions();

        return $this->render('formation/sessions.html.twig', [
            'formation' => $formation,
            'sessions' => $sessions,
        ]);
    }
}
