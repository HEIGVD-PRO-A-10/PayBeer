<?php

namespace App\Controller\Api;

use App\Entity\User;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Validator\Constraints\Json;

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
     * @Route("/new-user", name="new_user", methods={"GET"})
     * @param Request $request
     * @return Response
     */
    public function newUser(Request $request): Response {
        $entityManager = $this->getDoctrine()->getManager();
        $repository = $this->getDoctrine()->getRepository(User::class);

        $firstname = $request->query->get('firstname');
        $lastname = $request->query->get('lastname');
        $tagRfid = $request->query->get('tag_rfid');

        if (!empty($firstname) && !empty($lastname) && !empty($tagRfid)) {
            $existingUser = $repository->findOneBy(['tag_rfid' => $tagRfid]);
            if (!$existingUser) {
                $user = new User();
                $user
                    ->setFirstname($firstname)
                    ->setLastname($lastname)
                    ->setTagRfid($tagRfid)
                    ->setStatus('new');

                $entityManager->persist($user);
                $entityManager->flush();

                $response = new JsonResponse(['status' => 'success', 'message' => "$firstname $lastname a bien été enregistré avec le tag RFID $tagRfid"]);
                $response->setStatusCode(Response::HTTP_CREATED);
                return $response;
            } else {
                $response = new JsonResponse(['status' => 'error', 'message' => "Le tag RFID est déjà utilisé"]);
                $response->setStatusCode(Response::HTTP_BAD_REQUEST);
                return $response;
            }
        } else {
            $response = new JsonResponse(['status' => 'error', 'message' => "Paramètres incorrectes"]);
            $response->setStatusCode(Response::HTTP_BAD_REQUEST);
            return $response;
        }
    }
}
