<?php


namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;


class HomeController extends AbstractController {

    /**
     * @Route("/", name="home")
     */
    public function home() {


        $data =  array('route_name' => 'home', "color" => "red");
        return $this->render('home.html.twig', $data);
    }
}