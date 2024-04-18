<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Contracts\HttpClient\HttpClientInterface;
use Symfony\Component\Routing\Annotation\Route;
use App\Form\MailFormType;

class ForgotPasswordController extends AbstractController
{
    private $mailApi;
    private $client;
    public function __construct(HttpClientInterface $client, string $mailApi)
    {
        $this->mailApi = $mailApi;
        $this->client = $client;
    }
    #[Route('/forgot-password', name: 'app_forgot_password')]
    public function forgotPassword(Request $request): Response
    {
        $emailForm = $this->createForm(MailFormType::class);
        if($emailForm->isSubmitted() && $emailForm->isValid()){
            $email = $emailForm->getData();
            $this->client->request('POST', $this->mailApi, [
                'json' => ['email' => $email]
            ]);
        }
        return $this->render('login/forgot-password.html.twig', [
            'emailForm' => $emailForm->createView(),
            'controller_name' => 'ForgotPasswordController',
        ]);
    }


}
