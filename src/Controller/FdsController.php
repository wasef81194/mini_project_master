<?php

namespace App\Controller;

use App\Entity\Fds;
use App\Form\FdsType;
use App\Repository\FdsRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/fds')]
class FdsController extends AbstractController
{
    #[Route('/', name: 'app_fds_index', methods: ['GET'])]
    public function index(FdsRepository $fdsRepository): Response
    {
        return $this->render('fds/index.html.twig', [
            'fds' => $fdsRepository->findAll(),
        ]);
    }

    #[Route('/new', name: 'app_fds_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $fd = new Fds();
        $form = $this->createForm(FdsType::class, $fd);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($fd);
            $entityManager->flush();

            return $this->redirectToRoute('app_fds_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('fds/new.html.twig', [
            'fd' => $fd,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_fds_show', methods: ['GET'])]
    public function show(Fds $fd): Response
    {
        return $this->render('fds/show.html.twig', [
            'fd' => $fd,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_fds_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Fds $fd, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(FdsType::class, $fd);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('app_fds_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('fds/edit.html.twig', [
            'fd' => $fd,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_fds_delete', methods: ['POST'])]
    public function delete(Request $request, Fds $fd, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$fd->getId(), $request->request->get('_token'))) {
            $entityManager->remove($fd);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_fds_index', [], Response::HTTP_SEE_OTHER);
    }
    
}
