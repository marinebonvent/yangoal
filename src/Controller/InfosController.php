<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use App\Service\MailerService3;
use Symfony\Component\HttpFoundation\Request;



final class InfosController extends AbstractController
{
    private $mailerService3;


    public function __construct(MailerService3 $mailerService3 )
    {
        $this->mailerService3 = $mailerService3;
       

    }

    #[Route('/infos', name: 'app_infos')]
    public function index(): Response
    {
        return $this->render('infos/index.html.twig', [
            'controller_name' => 'InfosController',
        ]);
    }
    #[Route('/contacte', name: 'app_contacte')]
    public function contacte(Request $request): Response
{
    if ($request->isMethod('POST')) {
        $sender = $request->request->get('email');
        $message = $request->request->get('message');
        $captcha = $request->request->get('captcha');
        $captchaResult = $request->request->get('captcha_result');
        
        // Vérifier le captcha
        if ($captcha != $captchaResult) {
            $this->addFlash('error', 'La réponse à la question de sécurité est incorrecte.');
            return $this->render('infos/contacte.html.twig');
        }

        if ($sender && $message) {
            $emailBody = "<p><strong>Email:</strong> $sender</p><p><strong>Message:</strong> $message</p>";

            $this->mailerService3->SendMail($emailBody, $sender);

            $this->addFlash('success', 'Votre message a été envoyé avec succès !');
        } else {
            $this->addFlash('error', 'Veuillez remplir tous les champs.');
        }
    }

    return $this->render('infos/contacte.html.twig', [
        'controller_name' => 'InfosController',
    ]);
}

    #[Route('/cgv', name: 'app_cgv')]
    public function cgv(): Response
    {
        return $this->render('infos/cgv.html.twig', [
            'controller_name' => 'InfosController',
        ]);
    }
    #[Route('/paiement', name: 'app_paiement')]
    public function paiement(): Response
    {
        return $this->render('infos/paiement.html.twig', [
            'controller_name' => 'InfosController',
        ]);
    }
    #[Route('/politique', name: 'app_politique')]
    public function politique(): Response
    {
        return $this->render('infos/politique.html.twig', [
            'controller_name' => 'InfosController',
        ]);
    }
    #[Route('/livraison', name: 'app_livraison')]
    public function livraison(): Response
    {
        return $this->render('infos/livraison.html.twig', [
            'controller_name' => 'InfosController',
        ]);
    }
   
}
