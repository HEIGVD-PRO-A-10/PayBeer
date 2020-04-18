<?php


namespace App\Controller;


use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

class UsersController extends AbstractController {

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

    /**
     * @Route("/list", name="list_user")
     */
    public function listUsers() {

        $users = array(
            array(
                "id" => "1",
                "firstname" => "Thomas",
                "lastname" => "Rieder",
                "amount" => "1000",
                "isActiv" => "100"
            ),
            array(
                "id" => "2",
                "firstname" => "Julien",
                "lastname" => "Béguin",
                "amount" => "200",
                "isActiv" => "1"
            ),
            array(
                "id" => "3",
                "firstname" => "Nicolas",
                "lastname" => "Müller",
                "amount" => "300",
                "isActiv" => "1"
            ),
            array(
                "id" => "4",
                "firstname" => "Denis",
                "lastname" => "Bourqui",
                "amount" => "300",
                "isActiv" => "0"
            ),
            array(
                "id" => "5",
                "firstname" => "Gil",
                "lastname" => "Balsiger",
                "amount" => "300",
                "isActiv" => "1"
            )
        );

        $data =  array('current_page' => 'list_user', "users" => $users);
        return $this->render('listUser.html.twig', $data);
    }

}
