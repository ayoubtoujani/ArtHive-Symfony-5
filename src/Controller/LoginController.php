<?php

namespace App\Controller;
use App\Entity\Users;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use App\Form\LoginFormType;
use App\Form\RegisterFormType;
use \League\OAuth2\Client\Provider\Facebook;
use PhpParser\Node\Stmt\TryCatch;

class LoginController extends AbstractController
{

    private $provider;

    public function __construct()
    {
        $this->provider = new Facebook([
            'clientId'          => $_ENV['FCB_ID'],
            'clientSecret'      => $_ENV['FCB_SECRET'],
            'redirectUri'       => $_ENV['FCB_CALLBACK'],
            'graphApiVersion'   => 'v15.0',
        ]);
        
    }



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
                if($foundUser->getRole() == 'ROLE_ADMIN'){
                    return $this->redirectToRoute('app_admin');
                }
                else{
                    return $this->redirectToRoute('app_test');
                }
                return $this->redirectToRoute('app_test');
            } else {
                $error = 'Invalid email or password';
            }
        }

        if ($registerForm->isSubmitted() && $registerForm->isValid()) {
            $user = $registerForm->getData();
                
            $user->setPhoto('images/user.png');
            $user->setRole('ROLE_USER');
            $user->setBio('');

            // Additional validation or processing if needed before persisting
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($user);
            $entityManager->flush();

            $success = 'Registration successful';
            $session->set('user', $user);

            return $this->redirectToRoute('app_test');
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
        return $this->render('feed.html.twig', [
            'user' => $user,
            'nom' => $nom
        ]);
    }

    #[Route('/fcb-login', name: 'fcb_login')]
    public function loginFb():Response
    {
        $helper_url = $this->provider->getAuthorizationUrl();

        return $this->redirect($helper_url);
    }

    #[Route('/fcb-callback', name: 'fcb_callback')]
    public function callbackFb(SessionInterface $session):Response
    {
        // Try to get an access token (using the authorization code grant)
        $token = $this->provider->getAccessToken('authorization_code', [
            'code' => $_GET['code']
        ]);

        try {
            //Get user information
            $fbUser = $this->provider->getResourceOwner($token);
            $fbUser = $fbUser->toArray();
            $email = $fbUser['email'];
            $nom = $fbUser['first_name'];
            $prenom = $fbUser['last_name'];
            $email = $fbUser['email'];
            $photo = array($fbUser['picture_url']);

            //Check if user already exists
            $userRepository = $this->getDoctrine()->getRepository(Users::class);
            $foundUser = $userRepository->findOneBy(['email' => $email]);
            if($foundUser){
                $session->set('user', $foundUser);
            }else{
                //Create user to store in database
                $user = new Users();
                $user->setNomUser($nom);
                $user->setPrenomUser($prenom);
                $user->setMdpUser(sha1(str_shuffle('abcdefghjklmnopqrstuvwxyz1234567890')));
                $user->setEmail($email);
                $user->setDNaissanceUser(new \DateTime());
                $user->setNumTelUser('');
                $user->setVille('');
                $user->setBio('');
                $user->setRole('ROLE_USER');
                
                //Photo is changed with actual FB pfp
                $photoData = file_get_contents($photo[0]);
                if($photoData){
                    $fileName = uniqid() . '.jpg';
                    $filePath = $this->getParameter('images_directory') . '/' . $fileName;
                    if (file_put_contents($filePath, $photoData) === false) {
                        throw new \Exception('Error uploading file');
                    }
                    $user->setPhoto($fileName);
                }                

                $session->set('user', $user);
                $entityManager = $this->getDoctrine()->getManager();
                $entityManager->persist($user);
                try{
                    $entityManager->flush();
                }
                catch(\Exception $e){
                    dd($e);
                }
                
            }
        } catch (\Throwable $th) {
            //throw $th;
        }

        return $this->redirectToRoute('app_test');

    }

}



