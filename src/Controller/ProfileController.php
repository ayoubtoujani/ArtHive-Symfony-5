<?php

namespace App\Controller;
use App\Entity\Users;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\HttpFoundation\File\Exception\FileException;

use App\Form\EditProfileFormType;
use App\Repository\PublicationRepository;


class ProfileController extends AbstractController
{
    #[Route('/profile', name: 'app_profile')]
    public function index(SessionInterface $session,Request $request,PublicationRepository $publicationRepository): Response
    {
        $user = $session->get('user');
        $publications = $publicationRepository->findBy(['user' => $user], ['dCreationPublication' => 'DESC']);
        return $this->render('profile/profile.html.twig', [
            'controller_name' => 'ProfileController',
            'user' => $user,
            'publications' => $publications,
        ]);
    }
   


    #[Route('/profile/edit', name: 'app_profileEdit')]
    public function editProfile(Request $request, SessionInterface $session): Response
    {
        $logger = new \Symfony\Component\HttpKernel\Log\Logger();

        $userSession = $session->get('user');


        if($userSession instanceof Users){
            $userId = $userSession->getIdUser();
            $user = $this->getDoctrine()->getRepository(Users::class)->find($userId);
            $editProfileForm = $this->createForm(EditProfileFormType::class, $user);
            $editProfileForm->handleRequest($request);

            if($editProfileForm->isSubmitted() && $editProfileForm->isValid()) {
                $file = $editProfileForm->get('photo')->getData();
    
                if ($file) {
                    $newFilename = uniqid().'.'.$file->guessExtension();
                    try {
                        $file->move(
                            $this->getParameter('profile_images_directory'),
                            $newFilename
                        );
                    } catch (FileException $e) {
                        throw new \Exception('Error uploading file');
                    }
                    $user->setPhoto($newFilename);
                }

    
                //$user = $editProfileForm->getData();
                $entityManager = $this->getDoctrine()->getManager();
                //$entityManager->persist($user);
                try {
                    $entityManager->flush();
                    $logger->info(print_r($user, true));
                } catch (\Exception $e) {
                    $logger->error('Error saving user: '.$e->getMessage());
                    throw $e;
                }
                $session->set('user', $user);
                return $this->redirectToRoute('app_profile');
            }
        }

        
        return $this->render('profile/profileEdit.html.twig', [
            'controller_name' => 'ProfileController',
            'editProfileForm' => $editProfileForm->createView(),
            'user' => $user,
        ]);
    }

    private function sanitizeFileName($fileName) {
        // Remove special characters and spaces
        $fileName = preg_replace('/[^A-Za-z0-9_.-]/', '', $fileName);
        // Convert spaces to underscores
        $fileName = str_replace(' ', '_', $fileName);
        return $fileName;
    }
}
