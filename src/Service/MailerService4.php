<?php

namespace App\Service;

use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;

class MailerService4
{
    private $mailer;

    public function __construct(MailerInterface $mailer)
    {
        $this->mailer = $mailer;
    }

    public function sendMail(string $msg): void
    {
        $email = (new Email())
            ->from('fraktaalx@gmail.com')
            ->to('fraktaalx@gmail.com')
            ->subject('Notification Visite')
            ->html($msg);

        $this->mailer->send($email);
    }
}
