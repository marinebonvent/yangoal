<?php

namespace App\Service;

use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;



class MailerService2
{
    private $mailer;

    public function __construct(MailerInterface $mailer)
    {
        $this->mailer = $mailer;
    }

    public function sendConfirmationEmail($emailBody2)
    {
        $subject = 'une commmande ';

        $email = (new Email())
            ->from('fraktaalx@gmail.com')
 // Adresse e-mail de l'expéditeur
            ->to('fraktaalx@gmail.com')
 // Adresse e-mail du destinataire
            ->subject($subject) // Sujet de l'e-mail
            ->html($emailBody2);
         

        $this->mailer->send($email);
    }
}
