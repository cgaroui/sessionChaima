<?php

namespace App\Controller;

use App\Entity\Session;
use App\Entity\Programme;
use App\Entity\Stagiaire;
use App\Form\ProgrammeType;
use App\Entity\ModuleSession;
use App\Repository\SessionRepository;
use App\Repository\ProgrammeRepository;
use App\Repository\StagiaireRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class SessionController extends AbstractController
{

    #-----------------------------------------------------------------
    # Liste des sessions d'une formation
    #-----------------------------------------------------------------
    #[Route('/session', name: 'app_session')]
    public function index(EntityManagerInterface $entityManager): Response
    {
        $sessions = $entityManager->getRepository(Session::class)->findAll();

        return $this->render('session/index.html.twig', [
            'sessions' => $sessions,
        ]);
    }


    #-----------------------------------------------------------------
    # détail d'une session 
    #-----------------------------------------------------------------

    #[Route('/session/{id}', name: 'show_session')]
    public function showSession(Session $session = null, StagiaireRepository $stagiaireRepository, ProgrammeRepository $moduleRepository, SessionRepository $sr)
    {
        if($session) {
            // selectionner les stagiaires non inscrits et les mettres dans le tableau nonInscrits qui est vide au depart
            $nonInscrits = $sr->findNonInscrits($session->getId());
            
            // Filtrer les modules non programmés
            $nonProgrammes = $sr->findNonProgrammes($session->getId());
            // dd($nonProgrammes);
            return $this->render('session/show.html.twig', [
                'session' => $session,
                'non_inscrits' => $nonInscrits,
                'non_programmes' => $nonProgrammes,
            ]);
        } else {
            return $this->redirectToRoute("app_session");
        }
    }



    
    #-----------------------------------------------------------------
    # Liste des modules d'une session (le programme)
    #-----------------------------------------------------------------

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

    // #[Route('/session/{id}/ajouter-programme', name: 'add_programme_session')]
    // public function newProgramme(Request $request, Session $session, EntityManagerInterface $entityManager): Response
    // {
    //     $programme = new Programme();
    //     $form = $this->createForm(ProgrammeType::class, $programme);
        
    //     $form->handleRequest($request);

    //     //je verifie si mes champs ont été correctement remplis et mon formulaire bien soumis
    //     if ($form->isSubmitted() && $form->isValid()) {
    //         $programme->setSession($session); // Lie le programme à la session en cours

    //         $programme = $form->getData();
    //         //ici persist est equivalent à prepare(),il prepare la requete
    //         $entityManager->persist($programme);
    //         //methode flush pour exécuter la requete
    //         $entityManager->flush();

    //         $this->addFlash('success', 'Le programme a été ajouté avec succès.');

    //         //on retourn à la la page detail session où se trouve la liste des programme pour voi s'il s'est bien ajouté
    //         return $this->redirectToRoute('show_session', ['id' => $session->getId()]);
    //     }

    //     return $this->render('session/new_programme.html.twig', [
    //         'form' => $form->createView(),
    //         'session' => $session,
    //     ]);
    // }




    
  #[Route('/session/{id}/inscrire/{stagiaireId}', name:'inscrire_stagiaire')]
public function inscrire($id, $stagiaireId, EntityManagerInterface $em)
{
    // Récupérer la session et le stagiaire via leurs IDs
    $session = $em->getRepository(Session::class)->find($id);
    $stagiaire = $em->getRepository(Stagiaire::class)->find($stagiaireId);

    // Si la session ou le stagiaire n'est pas trouvé, afficher un message d'erreur
    if (!$session || !$stagiaire) {
        echo "Erreur : Session ou Stagiaire non trouvé"; // Affichage direct pour le débogage
        return new Response(); // Arrête l'exécution du code après l'affichage du message
    }

    //verifier le nombre de places disponibles 
    $nbPlacesDisponibles = $session->getNbPlacesDisponibles();

        if ($nbPlacesDisponibles <= 0) {
            // Pas assez de places disponibles
            return new Response("Erreur : Aucune place disponible pour cette session");
        }


    // Ajouter le stagiaire à la session
    $session->addStagiaire($stagiaire);

    // Enregistrer les modifications dans la base de données
    $em->persist($session);
   // mettre à jour le nb de places réservées
   $session->setPlacesReserve($session->getPlacesReserve() + 1);
   $em->persist($session);

   $em->flush();

    // Rediriger vers la page de détails de la session
    return $this->redirectToRoute('show_session', ['id' => $id]);
}



#[Route('/session/{id}/desinscrire/{stagiaireId}', name:'desinscrire_stagiaire')]
public function desinscrire($id, $stagiaireId, EntityManagerInterface $em){

    $session = $em->getRepository(Session::class)->find($id);
    $stagiaire = $em->getRepository(Stagiaire::class)->find($stagiaireId);

    if (!$session || !$stagiaire) {
        echo "Erreur : Session ou Stagiaire non trouvé"; // Affichage direct pour le débogage
        return new Response(); // Arrête l'exécution du code après l'affichage du message
    }

    //supprimer le stagiaire 
    $session->removeStagiaire($stagiaire);

    $em->persist($session);
    $em->flush();


    return $this->redirectToRoute('show_session', ['id' => $id]);

}


#[Route('/session/{id}/programmer/{moduleId}', name:'programmer_module')]
    public function programmer($id, $moduleId, EntityManagerInterface $em, Request $request)
    {

        $session = $em->getRepository(Session::class)->find($id);
        $module = $em->getRepository(ModuleSession::class)->find($moduleId);

        // dd($request);

        if (!$session || !$module) {
            echo "Erreur : Session ou module non trouvé"; // Affichage direct pour le débogage
            return new Response(); // Arrête l'exécution du code après l'affichage du message
        }

        if(isset($_POST["submit"])) {
            $programme = new Programme();
            // $module = new ModuleSession;
        
            $nombreJours = $request->request->get('nombre_jours');
            $nombreJours = filter_input(INPUT_POST, "nombre_jours", FILTER_VALIDATE_INT);
        
            //mettre à jour la valeur nombreJours
            $programme->setNbJours($nombreJours);
            $programme->setSession($session);
            $programme->setModule($module);
            
            $em->persist($programme);
            
            $session->addProgramme($programme);
            
            $em->persist($session);
            $em->flush();
        
            return $this->redirectToRoute('show_session', ['id' => $session->getId()]);
        }
    }



    #[Route('/session/{id}/deprogrammer/{moduleId}', name:'deprogrammer_module')]
    public function deprogrammer($id, $moduleId, EntityManagerInterface $em){

        $session = $em->getRepository(Session::class)->find($id);
        $module = $em->getRepository(ModuleSession::class)->find($moduleId);

        if (!$session || !$module) {
            echo "Erreur : Session ou Stagiaire non trouvé"; // Affichage direct pour le débogage
            return new Response(); // Arrête l'exécution du code après l'affichage du message
        }

        // Récupérer le programme associé à la session et au module
        $programmeRepository = $em->getRepository(Programme::class);
        $programme = $programmeRepository->findOneBy([
            'session' => $session,
            'module' => $module,
        ]);

        // Vérifier si le programme existe
        if (!$programme) {
            // Affichage d'un message d'erreur pour le débogage
            echo "Erreur : Programme non trouvé";
            // Arrête l'exécution du code après l'affichage du message
            return new Response(); 
        }

        // Supprimer le programme de la session
        $session->removeProgramme($programme);

        // Supprimer le programme de la base de données
        $em->remove($programme);
        $em->flush();

        // Rediriger vers la page de la session après la suppression
        return $this->redirectToRoute('show_session', ['id' => $session->getId()]);
        }
}  



