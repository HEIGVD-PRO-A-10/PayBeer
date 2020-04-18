<?php


namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;


class EditUserController extends AbstractController {

    /**
     * @Route("/edit/{userID}", name="edit_user")
     */
    public function editUser($userID) {

        //TODO: check id exist and get user info with id

        $data =  array(
            'current_page' => 'edit_user',
            'id' => $userID,
            'name' => 'Rieder',
            'firstname' => 'Thomas',
            'amount' => 1000,
            'active' => 1
        );
        return $this->render('editUser.html.twig', $data);
    }
}