<?php

namespace App\Controller;

use App\Entity\Utilisateur;
use App\Form\UtilisateurType;
use DateTime;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;
use Symfony\Component\Security\Core\Role\RoleHierarchyInterface;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;
use Symfony\Component\Security\Http\Event\InteractiveLoginEvent;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

class AuthController extends AbstractController
{
    #[Route('/connexion', name: 'app_login')]
    public function connexion(AuthenticationUtils $authenticationUtils, AuthorizationCheckerInterface $authorizationChecker): Response
    {
        $isAuth = $authorizationChecker->isGranted('IS_AUTHENTICATED_REMEMBERED');
        //Si l'utilisateur est déja connecter on le redirige vers la page d'acceuil
        if ($isAuth) {
            return $this->redirectToRoute('app_produit_index', [], Response::HTTP_SEE_OTHER);
        }
        // get the login error if there is one
        $error = $authenticationUtils->getLastAuthenticationError();

        //  last username entered by the user
        $lastUsername = $authenticationUtils->getLastUsername();
        return $this->render('auth/connexion.html.twig', [
            'last_username' => $lastUsername,
            'error'=> $error,
        ]);
    }

    #[Route('/inscription', name: 'app_registration')]
    public function inscription(Request $request, EntityManagerInterface $entityManager, RoleHierarchyInterface $roleHierarchy, UserPasswordHasherInterface $passwordHasher ): Response
    {
        $utilisateur = new Utilisateur();
        $form = $this->createForm(UtilisateurType::class, $utilisateur);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $utilisateur->setRoles($roleHierarchy->getReachableRoleNames(['ROLE_USER']));
            $utilisateur->setCreerLe(new \DateTime());
            //Hachage du mot de passe
            $hashedPassword = $passwordHasher->hashPassword(
                $utilisateur,
                $utilisateur->getPassword()
            );
            $utilisateur->setPassword($hashedPassword);
            $entityManager->persist($utilisateur);
            $entityManager->flush();

            return $this->redirectToRoute('app_login', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('auth/inscription.html.twig', [
            'utilisateur' => $utilisateur,
            'form' => $form,
        ]);
        
    }
}
