<?php


namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

class UsersListController extends AbstractController {

    /**
     * @Route("/list", name="list_user")
     */
    public function listUsers() {


        $data =  array('route_name' => 'list_user', "color" => "red");
        return $this->render('listUser.html.twig', $data);
    }

}