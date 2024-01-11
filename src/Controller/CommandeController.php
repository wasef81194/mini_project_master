<?php

namespace App\Controller;

use App\Entity\Commande;
use App\Entity\DetailCommande;
use App\Entity\ProduitPanier;
use App\Form\CommandeType;
use App\Repository\CommandeRepository;
use App\Repository\PanierRepository;
use App\Repository\ProduitPanierRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/commande')]
class CommandeController extends AbstractController
{
    #[Route('/', name: 'app_commande_index', methods: ['GET'])]
    public function index(CommandeRepository $commandeRepository): Response
    {
        return $this->render('commande/index.html.twig', [
            'commandes' => $commandeRepository->findAll(),
        ]);
    }

    #[Route('/new/{idPanier}', name: 'app_commande_new', methods: ['GET'])]
    public function new($idPanier, PanierRepository $panierRepository,ProduitPanierRepository $produitPanierRepository, EntityManagerInterface $entityManager): Response
    {
        $panier = $panierRepository->findOneBy(['id'=> $idPanier, 'utilisateur' => $this->getUser()]);
        $produitsPanier = $produitPanierRepository->findBy(['panier' => $panier, 'supprimer_le' => null]);
        //Créer la commande
        $commande = new Commande();
        $commande->setUtilisateur($this->getUser());
        $commande->setCreerLe(new \DateTime);
        $entityManager->persist($commande);
        $entityManager->flush();

        foreach ($produitsPanier as $produitPanier) {
            //Créer les details de la commande
            $detailCommande = new DetailCommande();
            $detailCommande->setCommande($commande);
            $detailCommande->setProduit($produitPanier->getProduit());
            $detailCommande->setQuantite($produitPanier->getQuantite());
            $detailCommande->setPrixUnitaire($produitPanier->getProduit()->getPrix());
            $detailCommande->setCreerLe(new \DateTime);
            $entityManager->persist($detailCommande);
            $entityManager->flush();

            //Supprime les produit du panier
            $produitPanier->setSupprimerLe(new \DateTime);
            $entityManager->persist($produitPanier);
            $entityManager->flush();
        }

        //Reset le panier
        $panier->setPrixTotal(0);
        $entityManager->persist($panier);
        $entityManager->flush();
      
        return $this->render('commande/new.html.twig', [
            'commande' => $commande,
            'message' => 'Votre commande à bien été prise en compte',
        ]);
    }

    #[Route('/{id}', name: 'app_commande_show', methods: ['GET'])]
    public function show(Commande $commande): Response
    {
        return $this->render('commande/show.html.twig', [
            'commande' => $commande,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_commande_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Commande $commande, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(CommandeType::class, $commande);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('app_commande_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('commande/edit.html.twig', [
            'commande' => $commande,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_commande_delete', methods: ['POST'])]
    public function delete(Request $request, Commande $commande, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$commande->getId(), $request->request->get('_token'))) {
            $entityManager->remove($commande);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_commande_index', [], Response::HTTP_SEE_OTHER);
    }
}
