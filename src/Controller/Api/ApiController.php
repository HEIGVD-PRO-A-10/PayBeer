<?php

namespace App\Controller\Api;

use App\Entity\User;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Class ApiController
 * @package App\Controller
 *
 * @Route("/api", name="api_")
 */
class ApiController extends AbstractController
{
    /**
     * @Route("/", name="index")
     */
    public function index(): Response {
        return new JsonResponse(['api_version' => '0.0.1']);
    }

    /**
     * @Route("/login", name="login")
     */
    public function login(): Response {
        return new Response("This is login");
    }

    /**
     * @Route("/debit", name="debit")
     */
    public function debit(): Response {

    }

    /**
     * @Route("/credit", name="credit")
     */
    public function credit(): Response {

    }

    /**
     * @Route("/new-user", name="new_user", methods={"POST"})
     * @param Request $request
     * @return Response
     */
    public function newUser(Request $request): Response {
        $entityManager = $this->getDoctrine()->getManager();

        $firstname = $request->request->get('firstname');
        $lastname = $request->request->get('lastname');
        $tagRfid = $request->request->get('tag_rfid');

        $user = new User();
        $user
            ->setFirstname($firstname)
            ->setLastname($lastname)
            ->setTagRfid($tagRfid)
            ->setStatus('new');

        $entityManager->persist($user);
        $entityManager->flush();

        return new JsonResponse(['status' => 'success', 'message' => "$firstname $lastname a bien été enregistré avec le tag RFID $tagRfid"]);
    }
}
