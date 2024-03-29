<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

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
    public function feed(): Response
    {
        return $this->render('feed.html.twig', [
            'blog_name' => 'BlogController',
        ]);
    }

}
