<?php

namespace App\Controller;
use App\Entity\Users;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class LoginController extends AbstractController
{
    #[Route('/login', name: 'app_login')]
    public function login(Request $request): Response
    {   
        $error = '';
                // Check if the form is submitted
                if ($request->isMethod('POST')) {
                    // Retrieve email and password from the request
                    $email = $request->request->get('email');
                    $password = $request->request->get('password');
        
                    // Retrieve user by email from the database
                    $userRepository = $this->getDoctrine()->getRepository(Users::class);
                    $user = $userRepository->findOneBy(['email' => $email]);
        
                    // If user exists and password matches
                    if ($user && $user->getMdpUser() === $password) {
                        // Log in the user
                        // You can store user information in session or cookie
                        // For example, $this->session->set('user', $user);
                        
                        // Redirect to a secured page
                        return $this->redirectToRoute('secured_page');
                    } else {
                        // Authentication failed, handle error
                        // For example, display error message
                        $error = 'Invalid email or password';

                    }
                }
        
                // Render login form
                return $this->render('login/login.html.twig', [
                    'error' => $error, // Pass the error variable to the template
                ]);
    }


}

