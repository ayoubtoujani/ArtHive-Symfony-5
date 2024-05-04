<?php

namespace App\Controller;

use App\Entity\Groups;
use App\Form\GroupsType;
use App\Repository\GroupsRepository;
use App\Repository\ReclamationgroupeRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/groups')]
class GroupsController extends AbstractController
{
    #[Route('/', name: 'app_groups_index', methods: ['GET'])]
    public function index(GroupsRepository $groupsRepository): Response
    {
        return $this->render('groups/index.html.twig', [
            'groups' => $groupsRepository->findAll(),
        ]);
    }
    #[Route('/groupsback', name: 'app_groups_indexback', methods: ['GET'])]
    public function indexback(GroupsRepository $groupsRepository): Response
    {
        return $this->render('groups/indexBack.html.twig', [
            'groups' => $groupsRepository->findAll(),
        ]);
    }


    #[Route('/{idGroup}/reclamations', name: 'app_groups_reclamations', methods: ['GET'])]
    public function showReclamations(ReclamationgroupeRepository $reclamationgroupeRepository, int $idGroup): Response
    {
        $reclamations = $reclamationgroupeRepository->findByGroupId($idGroup);

        // Render a template with the found reclamations
        return $this->render('groups/reclamations.html.twig', [
            'reclamations' => $reclamations,
        ]);
    }


    #[Route('/new', name: 'app_groups_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $group = new Groups();
        $form = $this->createForm(GroupsType::class, $group);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // Handle file upload
            $file = $form['image']->getData();
            $fileName = uniqid().'.'.$file->guessExtension();

            $file->move(
                $this->getParameter('image_directoryy'),
                $fileName
            );

            // Set the 'image' property with the file name
            $group->setImage($fileName);
            $entityManager->persist($group);
            $entityManager->flush();

            return $this->redirectToRoute('app_groups_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('groups/new.html.twig', [
            'group' => $group,
            'form' => $form,
        ]);
    }

    #[Route('/{idGroup}', name: 'app_groups_show', methods: ['GET'])]
    public function show(Groups $group): Response
    {
        return $this->render('groups/show.html.twig', [
            'group' => $group,
        ]);
    }

    #[Route('/{idGroup}/edit', name: 'app_groups_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Groups $group, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(GroupsType::class, $group);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $file = $form['image']->getData();

            if ($file) {
                $fileName = uniqid().'.'.$file->guessExtension();
                // Move the file to the directory where your images are stored
                $file->move(
                    $this->getParameter('image_directoryy'),
                    $fileName
                );
                // Set the 'image' property with the file name
                $group->setImage($fileName);
            }
            $entityManager->flush();

            return $this->redirectToRoute('app_groups_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('groups/edit.html.twig', [
            'group' => $group,
            'form' => $form,
        ]);
    }

    #[Route('/{idGroup}', name: 'app_groups_delete', methods: ['POST'])]
    public function delete(Request $request, Groups $group, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$group->getIdGroup(), $request->request->get('_token'))) {
            $entityManager->remove($group);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_groups_index', [], Response::HTTP_SEE_OTHER);
    }

//    #[Route('/group/search-results', name: 'app_group_search_results')]
//    public function searchResults(Request $request): Response
//    {
//        $searchTerm = $request->query->get('q');
//
//        $groups = $this->getDoctrine()
//            ->getRepository(Groups::class)
//            ->createQueryBuilder('g')
//            ->where('g.nomGroup LIKE :term')
//            ->orWhere('g.descriptionGroup LIKE :term')
//            ->setParameter('term', '%' . $searchTerm . '%')
//            ->getQuery()
//            ->getResult();
//
//        return $this->render('groups/search_results.html.twig', [
//            'groups' => $groups,
//            'searchTerm' => $searchTerm,
//        ]);
//    }

    #[Route('/group/search', name: 'app_group_search')]
    public function search(Request $request): Response
    {
        $searchTerm = $request->query->get('q');
        $searchBy = $request->query->get('search_by');

        $queryBuilder = $this->getDoctrine()->getRepository(Groups::class)->createQueryBuilder('g');

        if ($searchBy === 'name') {
            $queryBuilder->where('g.nomGroup LIKE :term')->setParameter('term', '%' . $searchTerm . '%');
        } elseif ($searchBy === 'description') {
            $queryBuilder->where('g.descriptionGroup LIKE :term')->setParameter('term', '%' . $searchTerm . '%');
        }

        $groups = $queryBuilder->getQuery()->getResult();

        return $this->render('groups/index.html.twig', [
            'groups' => $groups,
            'searchTerm' => $searchTerm,
            'searchBy' => $searchBy,
        ]);
    }
}
