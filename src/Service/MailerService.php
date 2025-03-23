<?php

namespace App\Service;

use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;



class MailerService
{
    private $mailer;

    public function __construct(MailerInterface $mailer)
    {
        $this->mailer = $mailer;
    }

    public function sendConfirmationEmail($toemail ,$numCommande , $subject,$emailBody)
    {
        $subject = 'Merci pour votre commande ';

        $email = (new Email())
            ->from('fraktaalx@gmail.com')
 // Adresse e-mail de l'expéditeur
            ->to($toemail)
 // Adresse e-mail du destinataire
            ->subject($subject) // Sujet de l'e-mail
            ->html($emailBody)
            ->attachFromPath('images/pdf/' . $numCommande . '.pdf'); // Chemin d'attachement

        $this->mailer->send($email);
    }
}