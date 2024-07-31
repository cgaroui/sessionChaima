<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\UserType;
use App\Form\UserPasswordType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class UserController extends AbstractController
{
    private $entityManager; // Déclaration d'une propriété pour stocker l'EntityManager

    // Constructeur de la classe qui reçoit l'EntityManager via l'injection de dépendances
    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;// Stockage de l'EntityManager dans la propriété
    }

    //  Méthode pour afficher le profil de l'utilisateur connecté
     #[Route('/profile', name: 'user_profile')]
     public function profile(Security $security): Response
     {
         // Récupérer l'utilisateur connecté
         $user = $security->getUser();
 
         // on s'assure que l'utilisateur est connecté
         if (!$user) {
             throw $this->createAccessDeniedException('Vous devez être connecté pour accéder à cette page.');
         }
 
         // Passer les informations de l'utilisateur à la vue
         return $this->render('user/profile.html.twig', [
            'user' => $user, //récuperer un utilisateur pour acceder à toutes ces informatons pour les afficheer dans la vue

         ]);
     }

    #[Route('/utilisateur/edition-mot-de-passe/{id}', name: 'user_edit')]
    public function editPassword(User $user, Request $request, UserPasswordHasherInterface $passwordHasher, EntityManagerInterface $entityManager): Response
    {
        // Création du formulaire pour la gestion des mots de passe
        $form = $this->createForm(UserType::class, $user);
        // Traitement de la requête HTTP pour le formulaire
        $form->handleRequest($request);

        // Vérifier si le formulaire a été soumis et est valide
        if ($form->isSubmitted() && $form->isValid()) {
            // Récupération de l'ancien mot de passe depuis le formulaire
            $oldPassword = $form->get('oldPassword')->getData();
            // Vérification si l'ancien mot de passe est valide pour l'utilisateur courant
            if ($passwordHasher->isPasswordValid($user, $oldPassword)) {
                // Récupération du nouveau mot de passe depuis le formulaire
                $newPassword = $form->get('plainPassword')->getData();
                // Définition du nouveau mot de passe haché pour l'utilisateur
                $user->setPassword(
                    $passwordHasher->hashPassword(
                        $user,
                        $newPassword
                    )
                );

                // Enregistrez l'utilisateur(avec mot de passe à jour)
                $entityManager->persist($user);
                $entityManager->flush();

                // Redirection 
                return $this->redirectToRoute('app_login');
            } else {
                // Gérez le cas où l'ancien mot de passe n'est pas valide
                $this->addFlash('error', 'Ancien mot de passe incorrect.');
            }
        }

        return $this->render('user/edit_password.html.twig', [
            'form' => $form->createView(),
            'user' => $user,
        ]);
    }


}

