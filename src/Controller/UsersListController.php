<?php


namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

class UsersListController extends AbstractController {

    /**
     * @Route("/list", name="list_user")
     */
    public function listUsers() {


        $data =  array('current_page' => 'list_user', "theme" => "red");
        return $this->render('listUser.html.twig', $data);
    }

}