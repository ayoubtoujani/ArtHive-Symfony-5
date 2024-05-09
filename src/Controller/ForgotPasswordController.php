<?php

namespace App\Controller;

use App\Services\MailApi;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use App\Form\MailFormType;


class ForgotPasswordController extends AbstractController
{
    private $mailApi;
    public function __construct(MailApi $mailApi)
    {
        $this->mailApi = $mailApi;
    }
    #[Route('/forgot-password', name: 'app_forgot_password')]
    public function forgotPassword(Request $request)
    {
        $emailForm = $this->createForm(MailFormType::class);
        if ($request->isMethod('POST')) {
            $email = $request->get('mail_form')['email'];
            try {
                $this->mailApi->sendPasswordEmail($email);
                $this->addFlash('success', 'Password reset email sent!');
            } catch (\Exception $e) {
                $this->addFlash('error', 'There was an error sending the password reset email.');
            }
        }

        return $this->render('login/forgot-password.html.twig', [
            'emailForm' => $emailForm->createView(),
            'controller_name' => 'ForgotPasswordController',
        ]);
    }


}
