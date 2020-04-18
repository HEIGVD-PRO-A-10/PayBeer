<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Class ApiController
 * @package App\Controller
 *
 * @Route("/api", name="api_")
 */
class ApiController extends AbstractController
{
    /**
     * @Route("/", name="index")
     */
    public function index() : Response
    {
        return $this->render('api/index.html.twig', [
            'controller_name' => 'ApiController',
        ]);
    }

    /**
     * @Route("/login", name="login")
     */
    public function login() : Response {
        return new Response("This is login");
    }

    /**
     * @Route("/debit", name="debit")
     */
    public function debit() {

    }

    /**
     * @Route("/credit", name="credit")
     */
    public function credit() {

    }

    /**
     * @Route("/new-user", name="new-user")
     */
    public function newUser() {

    }
}
