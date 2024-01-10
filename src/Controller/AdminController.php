<?php

namespace App\Controller;

use App\Entity\Fds;
use App\Entity\Produit;
use App\Form\ProduitType;
use App\Repository\FdsRepository;
use App\Repository\ProduitRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Validator\Constraints\DateTime;

#[Route('/admin')]
class AdminController extends AbstractController
{
    // #[Route('/', name: 'app_admin')]
    // public function index(): Response
    // {
        // return $this->render('admin/index.html.twig', [
            // 'controller_name' => 'AdminController',
        // ]);
    // }

    //Produits
    #[Route('/produit/new', name: 'app_produit_new', methods: ['GET', 'POST'])]
    public function newProduit(Request $request, EntityManagerInterface $entityManager): Response
    {
        $produit = new Produit();
        $error = null;
        $form = $this->createForm(ProduitType::class, $produit);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $fdsFile = $form->get('fdsFile')->getData();

            // Si un fichier à été téléversé
            if ($fdsFile) {
                $fds = new Fds();
                // this is needed to safely include the file name as part of the URL
                $newFilename = 'fds-'.$produit->getNom().'-'.uniqid().'.'.$fdsFile->guessExtension();
                // Move the file to the directory where brochures are stored
                try {
                    $fdsFile->move(
                        $this->getParameter('fds_directory'),
                        $newFilename
                    );
                } catch (FileException $e) {
                    $error = "Une erreur est survenu lors de l'upload du FDS";
                }
                $fds->setCheminPdf($newFilename);
                $fds->setCreerLe(new \DateTime);
                $fds->setProduit($produit);
            }
            $produit->setCreerLe(new \DateTime);
            $entityManager->persist($produit);
            $entityManager->flush();

            if (isset($fds)) {
                $entityManager->persist($fds);
                $entityManager->flush();
            }
            

            return $this->redirectToRoute('app_produit_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('produit/new.html.twig', [
            'produit' => $produit,
            'form' => $form,
            'error' => $error
        ]);
    }

    #[Route('/produit/{id}/edit', name: 'app_produit_edit', methods: ['GET', 'POST'])]
    public function editProduit(Request $request, Produit $produit, EntityManagerInterface $entityManager, FdsRepository $fdsRepository): Response
    {
        $cheminFichier =  $this->getParameter('fds_directory');
        $form = $this->createForm(ProduitType::class, $produit);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $fdsFile = $form->get('fdsFile')->getData();

            // Si un fichier à été téléversé
            if ($fdsFile) {
                $fds = new Fds();
                // this is needed to safely include the file name as part of the URL
                $newFilename = 'fds-'.$produit->getNom().'-'.uniqid().'.'.$fdsFile->guessExtension();
                // Move the file to the directory where brochures are stored
                try {
                    $fdsFile->move(
                        $this->getParameter('fds_directory'),
                        $newFilename
                    );
                   
                } catch (FileException $e) {
                    $error = "Une erreur est survenu lors de l'upload du FDS";
                }

                //Si un fds est telecharger ça supprime celui actuel
                $fdsDeletes = $fdsRepository->findFDSASupprimer($produit->getId());
                foreach ($fdsDeletes as $fdsDelete) {
                    $fdsDelete->setSupprimerLe(new \DateTime);
                }

                $fds->setCheminPdf($newFilename);
                $fds->setCreerLe(new \DateTime);
                $fds->setProduit($produit);
            }

            $entityManager->flush();
            
            if (isset($fds)) {
                $entityManager->persist($fds);
                $entityManager->flush();
            }
            return $this->redirectToRoute('app_produit_index', [], Response::HTTP_SEE_OTHER);
        }
        $fds = $fdsRepository->findOneFDS($produit->getId());
        return $this->render('produit/edit.html.twig', [
            'produit' => $produit,
            'form' => $form,
            'fds' => $fds,
            'cheminFichier' => $cheminFichier
        ]);
    }

    #[Route('/produit/{id}', name: 'app_produit_delete', methods: ['POST'])]
    public function deleteProduit(Request $request, Produit $produit, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$produit->getId(), $request->request->get('_token'))) {
            $produit->setSupprimerLe(new \DateTime);
            $entityManager->persist($produit);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_produit_index', [], Response::HTTP_SEE_OTHER);
    }

    //FDS
    #[Route('/fds/{id}', name: 'app_fds_delete', methods: ['POST'])]
    public function deleteFds(Request $request, Fds $fds, EntityManagerInterface $entityManager, ProduitRepository $produitRepository): Response
    {
        $produit = $fds->getProduit();
        $idPorduit = $produit->getId();
        if ($this->isCsrfTokenValid('delete'.$fds->getId(), $request->request->get('_token'))) {
            $fds->setSupprimerLe(new \DateTime);
            $entityManager->persist($fds);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_produit_edit', ['id' => $idPorduit ], Response::HTTP_SEE_OTHER);
    }
}
