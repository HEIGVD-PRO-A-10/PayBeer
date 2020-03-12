<?php


namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;


class AddUserController extends AbstractController {

    /**
     * @Route("/add", name="add_user")
     */
    public function addUser() {

        $data =  array('route_name' => 'add_user', "color" => "red");
        return $this->render('addUser.html.twig', $data);
    }
}