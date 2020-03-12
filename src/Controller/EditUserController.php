<?php


namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;


class EditUserController extends AbstractController {

    /**
     * @Route("/edit/{userID}", name="edit_user")
     */
    public function editUser($userID) {

        //TODO: check id exist

        $data =  array('route_name' => 'edit_user', "color" => "red");
        return $this->render('editUser.html.twig', $data);
    }
}