<?php

namespace App\Controller;

use App\Entity\Groups;
use App\Entity\Reclamationgroupe;
use App\Form\ReclamationgroupeType;
use App\Repository\ReclamationgroupeRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/reclamationgroupe')]
class ReclamationgroupeController extends AbstractController
{
    #[Route('/', name: 'app_reclamationgroupe_index', methods: ['GET'])]
    public function index(ReclamationgroupeRepository $reclamationgroupeRepository): Response
    {
        return $this->render('reclamationgroupe/index.html.twig', [
            'reclamationgroupes' => $reclamationgroupeRepository->findAll(),
        ]);
    }

    #[Route('/new/{groupId}', name: 'app_reclamationgroupe_newback', methods: ['GET', 'POST'])]
    public function newback(Request $request, EntityManagerInterface $entityManager, int $groupId): Response
    {
        $groupsRepository = $this->getDoctrine()->getRepository(Groups::class);
        $group = $groupsRepository->find($groupId);

        if (!$group) {
            throw $this->createNotFoundException('Group with id ' . $groupId . ' does not exist');
        }

        $reclamationgroupe = new Reclamationgroupe();
        $reclamationgroupe->setGroup($group); // Use setGroup() instead of setGroupId()

        $form = $this->createForm(ReclamationgroupeType::class, $reclamationgroupe);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($reclamationgroupe);
            $entityManager->flush();

            return $this->redirectToRoute('app_groups_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('reclamationgroupe/new.html.twig', [
            'reclamationgroupe' => $reclamationgroupe,
            'form' => $form,
        ]);
    }

    #[Route('/{idReclamation}', name: 'app_reclamationgroupe_show', methods: ['GET'])]
    public function show(Reclamationgroupe $reclamationgroupe): Response
    {
        return $this->render('reclamationgroupe/show.html.twig', [
            'reclamationgroupe' => $reclamationgroupe,
        ]);
    }

    #[Route('/{idReclamation}/edit', name: 'app_reclamationgroupe_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Reclamationgroupe $reclamationgroupe, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(ReclamationgroupeType::class, $reclamationgroupe);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('app_reclamationgroupe_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('reclamationgroupe/edit.html.twig', [
            'reclamationgroupe' => $reclamationgroupe,
            'form' => $form,
        ]);
    }

    #[Route('/{idReclamation}', name: 'app_reclamationgroupe_delete', methods: ['POST'])]
    public function delete(Request $request, Reclamationgroupe $reclamationgroupe, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$reclamationgroupe->getIdReclamation(), $request->request->get('_token'))) {
            $entityManager->remove($reclamationgroupe);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_reclamationgroupe_index', [], Response::HTTP_SEE_OTHER);
    }

}
