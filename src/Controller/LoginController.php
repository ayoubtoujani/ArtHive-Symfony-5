<?php

namespace App\Controller;
use App\Entity\Users;
use SessionIdInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use App\Form\LoginFormType;
use App\Form\RegisterFormType;
use Symfony\Component\Form\FormError;





class LoginController extends AbstractController
{

    #[Route('/login', name: 'app_login', methods:['POST'])]
    public function login(Request $request, SessionInterface $session): Response
    {
        $error = '';
        $success = ''; 

        $state= $request->get('state');
        $loginForm = $this->createForm(LoginFormType::class);
        $loginForm->handleRequest($request);

        $registerForm = $this->createForm(RegisterFormType::class);
        $registerForm->handleRequest($request);

        if ($loginForm->isSubmitted() && $loginForm->isValid()) {
            $user = $loginForm->getData();
            
            $email = $user->getEmail();
            $password = $user->getMdpUser();


            $userRepository = $this->getDoctrine()->getRepository(Users::class);
            $foundUser = $userRepository->findOneBy(['email' => $email]);

            if ($foundUser && $foundUser->getMdpUser() === $password) {
                $success = 'Login successful';
                $session->set('user', $foundUser);
                 // Check if user has admin role
                 if ($foundUser->getRole() === 'ROLE_ADMIN') {
                    return $this->redirectToRoute('app_admin'); // Redirect to admin dashboard
                } else {
                    return $this->redirectToRoute('afficher_publications'); // Redirect to regular user dashboard
                }
            } else {
                $error = 'Invalid email or password';
            }
        }

        if ($registerForm->isSubmitted() && $registerForm->isValid()) {
            $user = $registerForm->getData();
                
            $user->setPhoto('images/user.png');
            $user->setPhoto('images/user.png');
            $user->setRole('ROLE_USER');
            $user->setBio('');
            $user->setBio('');

            // Additional validation or processing if needed before persisting
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($user);
            $entityManager->flush();

            $success = 'Registration successful';
            $session->set('user', $user);

            return $this->redirectToRoute('afficher_publications');
            return $this->redirectToRoute('afficher_publications');
        }
        
        return $this->render('login/login.html.twig', [
            'loginForm' => $loginForm->createView(),
            'registerForm' => $registerForm->createView(),
            'state' => $state,
            'error' => $error,
            'success' => $success,
            
            
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