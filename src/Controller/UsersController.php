<?php


namespace App\Controller;


use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

class UsersController extends AbstractController {

    /**
     * @Route("/add", name="new_user_list")
     */
    public function addUser() {


        //todo: chek if add user form was send

        //list of user with new status
        $newUsers = array(
            array(
                "id" => "1",
                "tag_rfid" => "blblblb"
            ),
            array(
                "id" => "2",
                "tag_rfid" => "qwertzuiop"
            ),
            array(
                "id" => "3",
                "tag_rfid" => "asdfghjkl"
            ),
            array(
                "id" => "4",
                "tag_rfid" => "yxcvbn"
            ),
            array(
                "id" => "5",
                "tag_rfid" => "poiuztrew"
            )
        );

        $data =  array('current_page' => 'new_user_list', "users" => $newUsers);

        return $this->render('newUserList.html.twig', $data);
    }

    /**
     * @Route("/addUser/{id}/{tag_rfid}", name="add_user")
     */
    public function newUser($id, $tag_rfid) {
        $data =  array(
            'current_page' => 'add_user',
            'id' => $id,
            'tag_rfid' => $tag_rfid
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

        //todo: check if edit user form was send !

        //list of all users
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
