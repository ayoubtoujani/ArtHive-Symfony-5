<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
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
        return $this->render('admin/users.html.twig', [
            'controller_name' => 'AdminController',
        ]);
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
