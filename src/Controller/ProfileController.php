<?php

namespace App\Controller;
use App\Entity\Users;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Session\SessionInterface;

use App\Form\EditProfileFormType;

class ProfileController extends AbstractController
{
    #[Route('/profile', name: 'app_profile')]
    public function index(SessionInterface $session): Response
    {
        $user = $session->get('user');
        
        return $this->render('profile/profile.html.twig', [
            'controller_name' => 'ProfileController',
            'user' => $user,
        ]);
    }

    #[Route('/profile/edit', name: 'app_profileEdit')]
    public function editProfile(Request $request, SessionInterface $session): Response
    {
        $user = $session->get('user');
        $editProfileForm = $this->createForm(EditProfileFormType::class, $user);
        $editProfileForm->handleRequest($request);
        
        $data = json_decode($request->getContent(), true);

        if($editProfileForm->isSubmitted() && $editProfileForm->isValid()) {
            $file = $editProfileForm->get('photo')->getData();

            if ($file) {
                $fileName = md5(uniqid()).'.'.$file->guessExtension();
                $user->setPhoto($fileName);
            }

            $user = $editProfileForm->getData();
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($user);
            $entityManager->flush();
            $session->set('user', $user);
            return $this->redirectToRoute('app_profile');
        }

        
        return $this->render('profile/profileEdit.html.twig', [
            'controller_name' => 'ProfileController',
            'editProfileForm' => $editProfileForm->createView(),
            'user' => $user,
        ]);
    }
}
