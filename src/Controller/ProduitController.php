<?php

namespace App\Controller;

use App\Entity\Produit;
use App\Form\ProduitType;
use App\Repository\CommandeRepository;
use App\Repository\DetailCommandeRepository;
use App\Repository\FdsRepository;
use App\Repository\ProduitRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/produit')]
class ProduitController extends AbstractController
{
    #[Route('/', name: 'app_produit_index', methods: ['GET'])]
    public function index(ProduitRepository $produitRepository): Response
    {
        return $this->render('produit/index.html.twig', [
            'produits' => $produitRepository->findAllNotDelete(),
        ]);
    }

    

    #[Route('/user', name: 'app_produit_user')]
    public function showProduitCommande(FdsRepository $fdsRepository, CommandeRepository $commandeRepository, DetailCommandeRepository $detailCommandeRepository): Response
    {
        $commandes = $commandeRepository->findBy(['utilisateur' => $this->getUser(), 'supprimer_le' => null]);
        $produits = [];
        $fds = [];
        // parcours les commandes utilisateurs
        foreach ($commandes as $commande) {
            //Parcours les detail de la commande pour recupere les produit lier a celui-ci
            $detailsCommandes = $detailCommandeRepository->findBy(['commande' => $commande, 'supprimer_le' => null]);
            foreach ($detailsCommandes as $detailCommande) {
                //Si nous avons pas déja recupere le produit on le recupere
                if (!in_array($detailCommande->getProduit(), $produits)) {
                    $produit = $detailCommande->getProduit();
                    $produits[] = $produit;
                    $fds[$produit->getId()] = $fdsRepository->findOneBy(['supprimer_le' => null, 'produit'=> $produit]);
                }
            }
        }
        return $this->render('produit/show_commande.html.twig', [
            'produits' => $produits,
            'fds' => $fds,
        ]);
    }
    
    #[Route('/{id}', name: 'app_produit_show', methods: ['GET'])]
    public function show(Produit $produit): Response
    {
        return $this->render('produit/show.html.twig', [
            'produit' => $produit,
        ]);
    }

    
}
