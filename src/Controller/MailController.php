<?php


namespace App\Controller;

use Exception;
use Psr\Log\LoggerInterface;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Mailer\Exception\TransportExceptionInterface;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Address;
use Symfony\Component\Routing\Annotation\Route;

class MailController extends AbstractController
{

    /**
     * @Route("/mail")
     * @param MailerInterface $mailer
     * @param LoggerInterface $logger
     * @return Response
     * @throws Exception
     */
    public function mail(MailerInterface $mailer, LoggerInterface $logger) {
        $email = (new TemplatedEmail())
            ->from(Address::fromString('Gil Balsiger <gil.balsiger@heig-vd.ch>'))
            ->to(Address::fromString('Julien Béguin <julien.beguin@heig-vd.ch>'))
            ->subject('Email de test')
            ->htmlTemplate('email.html.twig')
            ->context([
                'expiration_date' => new \DateTime('+7 days'),
                'username' => 'jul0105',
            ])
            ->text('This is a text message');

        try {
            $mailer->send($email);
            $logger->info('Email sent successfully!');
            return new Response('<body><h1>Email sent successfully!</h1></body>');
        } catch (TransportExceptionInterface $e) {
            dd($e->getMessage());
        }
    }

}