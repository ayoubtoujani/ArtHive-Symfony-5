<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use App\Entity\Users;



class BlogController extends AbstractController
{
    #[Route('/blog', name: 'app_blog')]
    public function index(): Response
    {
        return $this->render('base.html.twig', [
            'blog_name' => 'BlogController',
        ]);
    }
    

   
    #[Route('/feed', name: 'app_feed')]
    public function feed(SessionInterface $session)
    {
        // Get the currently logged-in user from the session
        $user = $session->get('user');
    
        // Check if a user is logged in
        if ($user instanceof Users) {
            // User is logged in, you can access user instance for further functionality
    
            // Pass the user instance to the template
            return $this->render('feed.html.twig', [
                'blog_name' => 'BlogController',
                'user' => $user,
            ]);
        } else {
            // User is not logged in, handle accordingly
            // Redirect to login page or display an error message
            // Example:
            return $this->redirectToRoute('app_login');
        }
    }
  
   
    }