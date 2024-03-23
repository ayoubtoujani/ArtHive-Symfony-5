<?php

namespace App\Controller;
use App\Entity\Users;
use SessionIdInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Session\SessionInterface;




class LoginController extends AbstractController
{

    #[Route('/login', name: 'app_login', methods:['POST'])]
    public function login(Request $request, SessionInterface $session): Response
    {
        $error = '';
        $success = '';

        $email = $request->request->get('_email');
        $password = $request->request->get('_password');

        $userRepository = $this->getDoctrine()->getRepository(Users::class);
        $user = $userRepository->findOneBy(['email' => $email]);

        if ($user && $user->getMdpUser() === $password) {
            $success= 'Login successful';
            $session->set('user', $user);
            return $this->redirectToRoute('app_test');

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

    #[Route('/test', name: 'app_test')]
    public function home(SessionInterface $session): Response
    {
        $user = $session->get('user');
        if($user instanceof Users){
            $nom = $user->getNomUser();
        }else{
            $nom = 'unknown';
        }
        return $this->render('login/test.html.twig', [
            'user' => $user,
            'nom' => $nom
        ]);
    }

}

