<?php


namespace App\Controller;


use App\Repository\AdminRepository;
use App\Repository\TransactionRepository;
use App\Repository\UserRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
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
     * @param UserRepository $userRepository
     * @return Response
     */
    public function listUsers(Request $request, UserRepository $userRepository) {
        if($query = $request->query->get('q')) {
            $users = $userRepository->searchByLastnameOrFirstname($query);
        } else {
            $users = $userRepository->findAll();
        }
        return $this->render('listUser.html.twig', ['users' => $users]);
    }

}
