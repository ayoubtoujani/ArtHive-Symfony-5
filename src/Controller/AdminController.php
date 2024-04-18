<?php

namespace App\Controller;
use App\Entity\Users;
use App\Form\EditUserFormType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class AdminController extends AbstractController
{
    #[Route('/admin', name: 'app_admin')]
    public function index(): Response
    {
        return $this->render('admin/home.html.twig', [
            'controller_name' => 'AdminController',
        ]);
    }

    #[Route('/admin/users', name: 'app_admin_users', methods:['GET'])]
    public function getAllUsers()
    {
        $userRepository = $this->getDoctrine()->getRepository(Users::class);
        $users= $userRepository->findAll();

        return $this->render('admin/users.html.twig', [
            'users' => $users,
            'controller_name' => 'AdminController',
        ]);
    }

    #[Route('/admin/user/edit/{id}', name: 'app_admin_user_edit', methods:['POST'])]
    public function edit(Request $request, $id)
    {
        $userRepository = $this->getDoctrine()->getRepository(Users::class);
        $user = $userRepository->find($id);
        if (!$user) {
            throw $this->createNotFoundException('No user found for id '.$id);
        }

        $form = $this->createForm(EditUserFormType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->getDoctrine()->getManager()->flush();
            return $this->redirectToRoute('app_admin_users');
        }

        return $this->render('admin/user-edit.html.twig', [
            'user' => $user,
            'registerForm' => $form->createView(),
        ]);
    }

    #[Route('/admin/user/delete/{id}', name: 'app_admin_user_delete', methods:['POST'])]
    public function delete($id)
    {
        $entityManager = $this->getDoctrine()->getManager();
        $userRepository = $entityManager->getRepository(Users::class);
        $user = $userRepository->find($id);
        if (!$user) {
            throw $this->createNotFoundException('No user found for id '.$id);
        }

        $entityManager->remove($user);
        $entityManager->flush();
        return $this->redirectToRoute('app_admin_users');
    }



    #[Route('/admin/groups', name: 'app_admin_users', methods:['GET'])]
    public function getAllGroups()
    {
        return $this->render('admin/groups.html.twig', [
            'controller_name' => 'AdminController',
        ]);
    }


    #[Route('/admin/events', name: 'app_admin_users', methods:['GET'])]
    public function getAllEvents()
    {
        return $this->render('admin/events.html.twig', [
            'controller_name' => 'AdminController',
        ]);
    }

    #[Route('/admin/produits', name: 'app_admin_users', methods:['GET'])]
    public function getAllProducts()
    {
        return $this->render('admin/products.html.twig', [
            'controller_name' => 'AdminController',
        ]);
    }

    #[Route('/admin/reports', name: 'app_admin_users', methods:['GET'])]
    public function getAllReports()
    {
        return $this->render('admin/reports.html.twig', [
            'controller_name' => 'AdminController',
        ]);
    }

}
