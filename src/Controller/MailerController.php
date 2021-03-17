<?php

namespace App\Controller;

use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;
use Symfony\Component\Mime\Address;
use Symfony\Component\Mailer\Exception\TransportExceptionInterface;

class MailerController extends AbstractController
{
    /**
     * @Route("/email")
     */
    public function sendEmail(MailerInterface $mailer)
    {
        
            
        $email = (new Email())
        ->from(Address::fromString('Fabien Potencier <fabien@example.com>'))
        ->to('you@example.com')
        ->subject('Forget password?')
        //->cc('cc@example.com')
        //->bcc('bcc@example.com')
        //->replyTo('fabien@example.com')
        //->priority(Email::PRIORITY_HIGH)
        ->subject('Time for Symfony Mailer!')
        ->text('Sending emails is fun again!')
        ->html('<p>See Twig integration for better HTML integration!</p>');
        try {
            $mailer->send($email);
        } catch (TransportExceptionInterface $e) {
        // some error prevented the email sending; display an
        // error message or try to resend the message
        }
        // …
      return new Response(
          'Email was sent'
       );
    }
}