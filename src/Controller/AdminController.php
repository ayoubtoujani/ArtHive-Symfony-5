<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use App\Entity\Users;

class AdminController extends AbstractController
{
    #[Route('/admin', name: 'app_admin')]
    public function index(SessionInterface $session): Response
    {

            // Retrieve the user from the session
            $user = $session->get('user');

            // Check if a user is logged in
            if ($user instanceof Users) {
                //check to logged in user is admin
                if($user->getRole() ===  'ROLE_ADMIN'){
                    return $this->render('admin/home.html.twig', [
                        'controller_name' => 'AdminController',
                        'user' => $user,
                    ]);

                }else{
                    return $this->redirectToRoute('afficher_publications');
                }
            } else {
                return $this->redirectToRoute('app_login');
            }


    
           
    }
    #[Route('/admin/users', name: 'app_admin_users', methods:['GET'])]
    public function getAllUsers(SessionInterface $session): Response
    {
        // Retrieve the user from the session
        $user = $session->get('user');

        // Check if a user is logged in
        if ($user instanceof Users) {
    return $this->render('admin/users.html.twig', [
        'controller_name' => 'AdminController',
        'user' => $user,
    ]);
 } else {
    return $this->redirectToRoute('app_login');
 }
    }
    

    #[Route('/admin/posts', name: 'app_admin_users', methods:['GET'])]
    public function getAllPosts(SessionInterface $session): Response
    {
        // Retrieve the user from the session
        $user = $session->get('user');

        // Check if a user is logged in
        if ($user instanceof Users) {
    return $this->render('admin/posts.html.twig', [
        'controller_name' => 'AdminController',
        'user' => $user,
    ]);
} else {
    return $this->redirectToRoute('app_login');
}
    }

    #[Route('/admin/groups', name: 'app_admin_groups', methods:['GET'])]
    public function getAllGroups(SessionInterface $session): Response
    {
       
        // Retrieve the user from the session
        $user = $session->get('user');

        // Check if a user is logged in
        if ($user instanceof Users) {
    return $this->render('admin/groups.html.twig', [
        'controller_name' => 'AdminController',
        'user' => $user,
    ]);
} else {
    return $this->redirectToRoute('app_login');
}
    }


    #[Route('/admin/events', name: 'app_admin_users', methods:['GET'])]
    public function getAllEvents(SessionInterface $session): Response
    {
       // Retrieve the user from the session
       $user = $session->get('user');

       // Check if a user is logged in
       if ($user instanceof Users) {
   return $this->render('admin/events.html.twig', [
       'controller_name' => 'AdminController',
       'user' => $user,
   ]);
} else {
   return $this->redirectToRoute('app_login');
}
    }

    #[Route('/admin/produits', name: 'app_admin_users', methods:['GET'])]
    public function getAllProducts(SessionInterface $session): Response
    {
       
       // Retrieve the user from the session
       $user = $session->get('user');

       // Check if a user is logged in
       if ($user instanceof Users) {
   return $this->render('admin/products.html.twig', [
       'controller_name' => 'AdminController',
       'user' => $user,
   ]);
} else {
   return $this->redirectToRoute('app_login');
}
    }

    #[Route('/admin/reports', name: 'app_admin_users', methods:['GET'])]
    public function getAllReports(SessionInterface $session): Response
    {
       // Retrieve the user from the session
       $user = $session->get('user');

       // Check if a user is logged in
       if ($user instanceof Users) {
   return $this->render('admin/reports.html.twig', [
       'controller_name' => 'AdminController',
       'user' => $user,
   ]);
} else {
   return $this->redirectToRoute('app_login');
}
    }

}
