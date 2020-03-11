<?php


namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;


class EditController extends AbstractController {

    /**
     * @Route("/edit/{userID}", name="edit_user")
     */
    public function editUser($userID) {

        //TODO: check id exist
    }
}