<?php

namespace App\Controller;
use App\Entity\Users;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Session\SessionInterface;

class TestController extends AbstractController
{
    #[Route('/feed', name: 'app_feed')]
    public function index(SessionInterface $session): Response
    {
        $user = $session->get('user');
        
        return $this->render('feed.html.twig', [
            'controller_name' => 'TestController',
            'user' => $user,
        ]);
    }
}
