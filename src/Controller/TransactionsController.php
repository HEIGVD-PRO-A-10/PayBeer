<?php


namespace App\Controller;


use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

class TransactionsController extends AbstractController {

    /**
     * @Route("/", name="home")
     */
    public function home() {
        $data =  array('current_page' => 'home', "theme" => "red");
        return $this->render('home.html.twig', $data);
    }

    /**
     * @Route("/history", name="history")
     */
    public function history() {
        $data =  array('current_page' => 'history', "theme" => "red");
        return $this->render('history.html.twig', $data);
    }

}
