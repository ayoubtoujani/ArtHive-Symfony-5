<?php

namespace App\Controller;

use App\Entity\Publications;
use App\Form\PublicationType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Repository\PublicationRepository;
use Doctrine\Persistence\ManagerRegistry;

class PublicationsController extends AbstractController
{
    #[Route('/publications', name: 'publications_index', methods: ['GET'])]
    public function index(PublicationRepository $publicationRepository): Response
    {
        $publications = $publicationRepository->findAll();

        return $this->render('publications/afficherPublications.html.twig', [
            'publications' => $publications,
        ]);
    }
/*
    #[Route('/publications/new', name: 'publications_new', methods: ['GET', 'POST'])]
    public function new(Request $request, ManagerRegistry $manager): Response
    {
        $publication = new Publications();
        $form = $this->createForm(PublicationType::class, $publication);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager = $manager->getManager();
            $entityManager->persist($publication);
            $entityManager->flush();

            return $this->redirectToRoute('publications_index');
        }

        return $this->render('publications/new.html.twig', [
            'publication' => $publication,
            'form' => $form->createView(),
        ]);
    }

    #[Route('/publications/{id}', name: 'publications_show', methods: ['GET'])]
    public function show(Publications $publication): Response
    {
        return $this->render('publications/show.html.twig', [
            'publication' => $publication,
        ]);
    }

    #[Route('/publications/{id}/edit', name: 'publications_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Publications $publication, ManagerRegistry $manager): Response
    {
        $form = $this->createForm(PublicationType::class, $publication);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager = $manager->getManager();
            $entityManager->flush();

            return $this->redirectToRoute('publications_index');
        }

        return $this->render('publications/edit.html.twig', [
            'publication' => $publication,
            'form' => $form->createView(),
        ]);
    }

    #[Route('/publications/{id}', name: 'publications_delete', methods: ['POST'])]
    public function delete(Request $request, Publications $publication, ManagerRegistry $manager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$publication->getIdPublication(), $request->request->get('_token'))) {
            $entityManager = $manager->getManager();
            $entityManager->remove($publication);
            $entityManager->flush();
        }

        return $this->redirectToRoute('publications_index');
    }
    */
}
