<?php


namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Mailer\Exception\TransportExceptionInterface;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Address;
use Symfony\Component\Mime\Email;
use Symfony\Component\Routing\Annotation\Route;

class MailController extends AbstractController
{

    /**
     * @Route("/mail")
     * @param MailerInterface $mailer
     */
    public function mail(MailerInterface $mailer) {
        $email = (new Email())
            ->from(Address::fromString('Gil Balsiger <gil.balsiger@heig-vd.ch>'))
            ->to('albert@bluewin.ch')
            ->subject('Email de test')
            ->text('This is a text message')
            ->html('<p>This is an <b>HTML</b> <em>message</em></p>');

        try {
            $mailer->send($email);
            dd('Email sent successfully');
        } catch (TransportExceptionInterface $e) {
            dd($e->getMessage());
        }
    }

}