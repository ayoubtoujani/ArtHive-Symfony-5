<?php

namespace App\Controller;
use App\Entity\Users;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;



class LoginController extends AbstractController
{

    #[Route('/login', name: 'app_login', methods:['POST'])]
    public function login(Request $request): Response
    {
        $error = '';
        $success = '';

        $email = $request->request->get('_email');
        $password = $request->request->get('_password');

        $userRepository = $this->getDoctrine()->getRepository(Users::class);
        $user = $userRepository->findOneBy(['email' => $email]);

        if ($user && $user->getMdpUser() === $password) {
            $success= 'Login successful';

        } else {
            $error = 'Invalid email or password';

        }

        return $this->render('login/login.html.twig', [
            'error'         => $error,
            'success'       => $success,
        ]);

    }
    #[Route('/login_check', name: 'app_login_check')]
    public function loginCheck()
    {
        // This code will not be executed
    }

    #[Route('/logout', name: 'app_logout')]
    public function logout()
    {
        // This code will not be executed
    }

}

