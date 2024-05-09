<?php

namespace App\Controller;

use App\Entity\Groups;
use App\Entity\Users;
use App\Form\GroupsType;
use App\Repository\GroupsRepository;
use App\Repository\ReclamationgroupeRepository;
use Doctrine\ORM\EntityManagerInterface;
use DrupalCodeGenerator\Command\Service\ResponsePolicy;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\RedirectResponse;

#[Route('/groups')]
class GroupsController extends AbstractController
{
    #[Route('/', name: 'app_groups_index', methods: ['GET'])]
    public function index(GroupsRepository $groupsRepository, SessionInterface $session): Response
    {
        //get the usseer from the session
        $user = $session->get('user');

        //if the user is not logged in, redirect to the login page
        if ($user instanceof Users) {
        return $this->render('groups/index.html.twig', [
            'groups' => $groupsRepository->findAll(),
            'user' => $user,
        ]);
    }
    return $this->redirectToRoute('app_login');
    }

    #[Route('/groupsback', name: 'app_groups_indexback', methods: ['GET'])]
    public function indexback(GroupsRepository $groupsRepository): Response
    {
        return $this->render('groups/indexBack.html.twig', [
            'groups' => $groupsRepository->findAll(),

        ]);
    }


    #[Route('/{idGroup}/reclamations', name: 'app_groups_reclamations', methods: ['GET'])]
    public function showReclamations(ReclamationgroupeRepository $reclamationgroupeRepository, int $idGroup, SessionInterface $session): Response
    {
        $reclamations = $reclamationgroupeRepository->findByGroupId($idGroup);

        // Render a template with the found reclamations
        return $this->render('groups/reclamations.html.twig', [
            'reclamations' => $reclamations,
            'user' => $session->get('user'),
        ]);
    }


    #[Route('/new', name: 'app_groups_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager , SessionInterface $session ): Response
    {

        //get the usseer from the session
        $user = $session->get('user');

        if ($user instanceof Users) {
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
            // Fetch the user from the database
            $userRepository = $this->getDoctrine()->getRepository(Users::class);
            $userFromDb = $userRepository->find($user->getIdUser());
            // Set the user as the owner of the group
            $group->setUser($userFromDb);
            $entityManager->persist($group);
            $entityManager->flush();

            return $this->redirectToRoute('app_groups_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('groups/new.html.twig', [
            'group' => $group,
            'form' => $form,
            'user' => $user,

        ]);
    }
    return $this->redirectToRoute('app_login');
    }
     
    #[Route('/{idGroup}', name: 'app_groups_show', methods: ['GET'])]
public function show($idGroup, SessionInterface $session): Response
{
    $group = $this->getDoctrine()->getRepository(Groups::class)->find($idGroup);
    return $this->render('groups/show.html.twig', [
        'group' => $group,
        'user' => $session->get('user'),
    ]);
}

    #[Route('/{idGroup}/edit', name: 'app_groups_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, $idGroup, EntityManagerInterface $entityManager , SessionInterface $session , GroupsRepository $groupsRepository):Response
    {

        $group = $groupsRepository->find($idGroup);
        //get the usseer from the session
        $user = $session->get('user');

        if ($user instanceof Users) {


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
            'user' => $user,
        ]);

    }
    return $this->redirectToRoute('app_login');

    }

    #[Route('/{idGroup}', name: 'app_groups_delete', methods: ['POST'])]
    public function delete($idGroup,Request $request, EntityManagerInterface $entityManager): RedirectResponse
    {

       $group = $entityManager->getRepository(Groups::class)->find($idGroup);

       if (!$group) {
           throw $this->createNotFoundException('No group found for id '.$idGroup);
       }
       
       $entityManager = $this->getDoctrine()->getManager();
       $entityManager->remove($group);
       $entityManager->flush();

         return $this->redirectToRoute('app_groups_index');
       
    }
   
    #[Route('/group/search', name: 'app_group_search')]
    public function search(Request $request,SessionInterface $session): Response
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
            'user' => $session->get('user'),
        ]);
    }
    
}
