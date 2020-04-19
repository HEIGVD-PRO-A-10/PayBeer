<?php


namespace App\Controller;


use App\Repository\UserRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class UsersController extends AbstractController {

    /**
     * @Route("/add", name="user_add")
     */
    public function addUser() {
        $data =  array(
            'current_page' => 'add_user',
            "id" => "123"
        );
        return $this->render('addUser.html.twig', $data);
    }

    /**
     * @Route("/user/{userID}/edit", name="user_edit", methods={"GET"})
     * @param $userID
     * @param UserRepository $userRepository
     * @return Response
     */
    public function editUser($userID, UserRepository $userRepository) {
        $user = $userRepository->find($userID);
        return $this->render('editUser.html.twig', ['user' => $user]);
    }

    /**
     * @Route("/user/{userID}/edit", name="user_edit_post", methods={"POST"})
     * @param $userID
     * @param Request $request
     * @param UserRepository $userRepository
     * @return Response
     */
    public function editUserPost($userID, Request $request, UserRepository $userRepository) {
        $req = $request->request;
        if(($firstname = $req->get('firstname')) && ($lastname = $req->get('lastname'))) {
            $entityManager = $this->getDoctrine()->getManager();
            $user = $userRepository->find($userID);
            if($user) {
                $user->setFirstname($firstname);
                $user->setLastname($lastname);
                $entityManager->flush();
            }
        }
        $this->addFlash('success', 'Utilisateur modifié avec succès');
        return $this->redirectToRoute('user_info', ['userID' => $userID]);
    }

    /**
     * @Route("/user/{userID}", name="user_info")
     * @param $userID
     * @param UserRepository $userRepository
     * @return Response
     */
    public function info($userID, UserRepository $userRepository) {
        $user = $userRepository->find($userID);
        return $this->render('infoUser.html.twig', ['user' => $user]);
    }

    /**
     * @Route("/list", name="user_list")
     * @param UserRepository $userRepository
     * @return Response
     */
    public function listUsers(Request $request, UserRepository $userRepository) {
        if($query = $request->query->get('q')) {
            $users = $userRepository->searchByLastnameOrFirstname($query);
            return $this->render('listUser.html.twig', ['users' => $users, 'query' => $query]);
        } else {
            $users = $userRepository->findAll();
            return $this->render('listUser.html.twig', ['users' => $users]);
        }
    }

    /**
     * @Route("/user/{userID}/lock", name="user_lock")
     */
    public function lock($userID, UserRepository $userRepository) {
        $entityManager = $this->getDoctrine()->getManager();
        $user = $userRepository->find($userID);
        $user->setStatus('BLOCKED');
        $entityManager->flush();
        $this->addFlash('success', "<i class='fa fa-user-lock'></i> {$user->getFirstname()} {$user->getLastname()} est maintenant bloqué");
        return $this->redirectToRoute('user_info', ['userID' => $userID]);
    }

    /**
     * @Route("/user/{userID}/unlock", name="user_unlock")
     */
    public function unlock($userID, UserRepository $userRepository) {
        $entityManager = $this->getDoctrine()->getManager();
        $user = $userRepository->find($userID);
        $user->setStatus('ACTIVE');
        $entityManager->flush();
        $this->addFlash('success', "<i class='fa fa-unlock'></i> {$user->getFirstname()} {$user->getLastname()} est maintenant débloqué");
        return $this->redirectToRoute('user_info', ['userID' => $userID]);
    }

    /**
     * @Route("/user/{userID}/delete", name="user_delete")
     */
    public function delete($userID, UserRepository $userRepository) {
        /*$entityManager = $this->getDoctrine()->getManager();
        $user = $userRepository->find($userID);
        $entityManager->remove($user);
        $entityManager->flush();*/
        $this->addFlash('danger', 'Les utilisateurs ne peuvent pas être supprimés pour l\'instant');
        return $this->redirectToRoute('user_list');
    }

}
