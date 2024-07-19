<?php

namespace App\Controller;

use App\Entity\Session;
use App\Entity\Programme;
use App\Entity\Stagiaire;
use App\Form\ProgrammeType;
use App\Repository\ProgrammeRepository;
use App\Repository\StagiaireRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class SessionController extends AbstractController
{
    #[Route('/session', name: 'app_session')]
    public function index(EntityManagerInterface $entityManager): Response
    {
        $sessions = $entityManager->getRepository(Session::class)->findAll();

        return $this->render('session/index.html.twig', [
            'sessions' => $sessions,
        ]);
    }

    #[Route('/session/{id}', name: 'show_session')]
    public function showSession(Session $session, StagiaireRepository $stagiaireRepository, ProgrammeRepository $moduleRepository)
{
    // Obtenir tous les stagiaires
    $allStagiaires = $stagiaireRepository->findAll();

    // Obtenir tous les modules
    $allModules = $moduleRepository->findAll();

    // Filtrer les stagiaires non inscrits
    $nonInscrits = array_filter($allStagiaires, function ($stagiaire) use ($session) {
        return !$session->getStagiaires()->contains($stagiaire);
    });

    // Filtrer les modules non programmés
    $nonProgrammes = array_filter($allModules, function ($module) use ($session) {
        return !$session->getProgrammes()->contains($module);
    });

    return $this->render('session/show.html.twig', [
        'session' => $session,
        'non_inscrits' => $nonInscrits,
        'non_programmes' => $nonProgrammes,
    ]);
}



    #[Route('/session/{id}/programmes', name: 'session_programmes')]
    public function programmes(Session $session): Response
    {
        $programmes = $session->getProgrammes();

        return $this->render('session/programmes.html.twig', [
            'session' => $session,
            'programmes' => $programmes,
        ]);
    }

    #[Route('/programme', name: 'app_programme')]
    public function programmesList(EntityManagerInterface $entityManager): Response
    {
        $programmes = $entityManager->getRepository(Programme::class)->findAll();

        return $this->render('programme/index.html.twig', [
            'programmes' => $programmes,
        ]);
    }

    #[Route('/programme/{id}', name: 'show_programme')]
    public function showProgramme(Programme $programme): Response
    {
        return $this->render('programme/show.html.twig', [
            'programme' => $programme,
        ]);
    }

    #[Route('/session/{id}/ajouter-programme', name: 'add_programme_session')]
    public function newProgramme(Request $request, Session $session, EntityManagerInterface $entityManager): Response
    {
        $programme = new Programme();
        $form = $this->createForm(ProgrammeType::class, $programme);
        
        $form->handleRequest($request);

        //je verifie si mes champs ont été correctement remplis et mon formulaire bien soumis
        if ($form->isSubmitted() && $form->isValid()) {
            $programme->setSession($session); // Lie le programme à la session en cours

            $programme = $form->getData();
            //ici persist est equivalent à prepare(),il prepare la requete
            $entityManager->persist($programme);
            //methode flush pour exécuter la requete
            $entityManager->flush();

            $this->addFlash('success', 'Le programme a été ajouté avec succès.');

            //on retourn à la la page detail session où se trouve la liste des programme pour voi s'il s'est bien ajouté
            return $this->redirectToRoute('show_session', ['id' => $session->getId()]);
        }

        return $this->render('session/new_programme.html.twig', [
            'form' => $form->createView(),
            'session' => $session,
        ]);
    }


    

}  

