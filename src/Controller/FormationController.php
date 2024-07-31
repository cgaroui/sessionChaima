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
    public function show(Formation $formation = null): Response
    { 
        if($formation){
            return $this->render('formation/show.html.twig', [
                'formation' => $formation,
            ]);
        }else{
            return $this->redirectToRoute("app_formation");
        }
    }

    #[Route('/formation/{id}/sessions', name: 'formation_sessions')]
    public function sessions(Formation $formation = null): Response
    {
        //on verifie si la formation exxiste on affiche la liste de ses sessions
        if($formation){

            $sessions = $formation->getSessions();
    
            return $this->render('formation/sessions.html.twig', [
                'formation' => $formation,
                'sessions' => $sessions,
            ]);
        }else{
            //sinon on redirige vers lq liste des formation (ce if assure la sécurité en cas dentré un autre id dans l'url)
            return $this->redirectToRoute("app_formation");
        }
    }
}
