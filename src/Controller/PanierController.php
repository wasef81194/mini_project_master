<?php

namespace App\Controller;

use App\Entity\Panier;
use App\Entity\Produit;
use App\Entity\ProduitPanier;
use App\Form\PanierType;
use App\Repository\PanierRepository;
use App\Repository\ProduitPanierRepository;
use App\Repository\ProduitRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;

#[Route('/panier')]
class PanierController extends AbstractController
{
    #[Route('/', name: 'app_panier_show', methods: ['GET'])]
    public function show(PanierRepository $panierRepository, ProduitPanierRepository $produitPanierRepository, EntityManagerInterface $entityManager): Response
    {
        $panier = $panierRepository->findOneBy(['utilisateur' => $this->getUser(), 'supprimer_le' => null]);
        if(!$panier){
            // on creer à l'utilisateur un panier par dafault
            $panier = new Panier();
            $panier->setCreerLe(new \DateTime);
            $panier->setUtilisateur($this->getUser());
            $panier->setPrixTotal(0);
            $entityManager->persist($panier);
            $entityManager->flush();
        }

        $produitsPanier = $produitPanierRepository->findBy(['panier' => $panier, 'supprimer_le' => null]);
       
        return $this->render('panier/show.html.twig', [
            'panier' => $panier,
            'produitsPanier' => $produitsPanier
        ]);
    }

    #[Route('/add/{idProduit}', name: 'app_panier_add', methods: ['GET', 'POST'])]
    public function add($idProduit, ProduitPanierRepository $produitPanierRepository, EntityManagerInterface $entityManager, PanierRepository $panierRepository, ProduitRepository $produitRepository, AuthorizationCheckerInterface $authorizationChecker): Response
    {
       
        //Si l'utilisateur n'est pas connecté
        $isAuth = $authorizationChecker->isGranted('IS_AUTHENTICATED_REMEMBERED');
        if (!$isAuth) {
            return $this->redirectToRoute('app_login', [], Response::HTTP_SEE_OTHER);
        }

        
        $panier = $panierRepository->findOneByUser($this->getUser()->getId());
        //Si l'utilisateur à pas de panier
        if (!$panier) {
            // On lui créer
            $panier = new Panier();
            $panier->setCreerLe(new \DateTime);
            $panier->setUtilisateur($this->getUser());
            $panier->setPrixTotal(0);
        }

        //On cherche le produit
        $produit = $produitRepository->findOneBy(['id' => $idProduit, 'supprimer_le' => null]);
        if(!$produit){
            dd('Erreur produit innexistant');
        }
        //Calcul le prix total
        $prixTotal = $produit->getPrix()+$panier->getPrixTotal();
        $panier->setPrixTotal($prixTotal);
        $entityManager->persist($panier);
        $entityManager->flush();

        //On ajoute le produit
        $produitPanier = $produitPanierRepository->findOneBy(['panier' => $panier->getId(), 'produit' => $produit->getId() , 'supprimer_le' => null]);
        //Si li existe déja on change juste la quantité
        if ($produitPanier) {
            $produitPanier->setQuantite($produitPanier->getQuantite() + 1);
        }
        else{
             //Si non on ajoute
            $produitPanier = new ProduitPanier();
            $produitPanier->setPanier($panier);
            $produitPanier->setProduit($produit);
            $produitPanier->setQuantite(1);
            $produitPanier->setCreerLe(new \DateTime);
        }

        $entityManager->persist($produitPanier);
        $entityManager->flush();

        return $this->redirectToRoute('app_produit_show', ['id' => $idProduit ], Response::HTTP_SEE_OTHER);
    }

    #[Route('/retirer/produit/{idProduit}', name: 'app_panier_retirer_produit', methods: ['GET'])]
    public function retirerProduit($idProduit, PanierRepository $panierRepository, ProduitPanierRepository $produitPanierRepository, EntityManagerInterface $entityManager): Response
    {
        $panier = $panierRepository->findOneBy(['utilisateur' => $this->getUser(), 'supprimer_le' => null]);
        $produitPanier = $produitPanierRepository->findOneBy(['produit' => $idProduit, 'panier' => $panier]);
        $produitPanier->setSupprimerLe(new \DateTime);
        $entityManager->persist($produitPanier);
        $entityManager->flush();
        return $this->redirectToRoute('app_panier_show', [], Response::HTTP_SEE_OTHER);   
    }
}
