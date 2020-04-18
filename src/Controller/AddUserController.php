<?php


namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;


class AddUserController extends AbstractController {

    /**
     * @Route("/add", name="add_user")
     */
    public function addUser() {
        $data =  array(
            'current_page' => 'add_user',
            "id" => "123"
        );
        return $this->render('addUser.html.twig', $data);
    }
}