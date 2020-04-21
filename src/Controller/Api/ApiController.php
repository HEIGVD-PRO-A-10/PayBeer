<?php

namespace App\Controller\Api;

use App\Entity\Admin;
use App\Entity\Transaction;
use App\Entity\User;
use App\Kernel;
use App\Repository\AdminRepository;
use App\Repository\UserRepository;
use DateTime;
use Exception;
use http\Env;
use Lcobucci\JWT\Builder;
use Lcobucci\JWT\Parser;
use Lcobucci\JWT\Signer\Hmac\Sha256;
use Lcobucci\JWT\Signer\Key;
use Lcobucci\JWT\ValidationData;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

use OpenApi\Annotations as OA;


/**
 * Class ApiController
 * @package App\Controller
 *
 * @Route("/api", name="api_")
 */
class ApiController extends AbstractController {

    /**
     * @Route("/", name="docs")
     */
    public function index() {
        return $this->redirect('/api/index.html', Response::HTTP_MOVED_PERMANENTLY);
    }

    /**
     * @Route("/login", name="login", methods={"POST"})
     *
     * @OA\Post(
     *     path="/login",
     *     summary="Authentification à l'API",
     *     description="Il est nécessaire d'appeler cette route afin de s'authentifier à l'API. Celle-ci renvoie un token JWT en cas de succès. Ce token est valable 1 heure par défaut.",
     *     @OA\RequestBody(
     *         @OA\MediaType(
     *             mediaType="application/x-www-form-urlencoded",
     *             @OA\Schema(
     *                 type="object",
     *                 @OA\Property(property="tag_rfid", type="string", description="Tag RFID", example="123456"),
     *                 @OA\Property(property="pin_number", type="string", description="Numéro d'identification personnel", example="32164"),
     *                 required={"tag_rfid", "pin_number"}
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response="200",
     *         description="Token JWT",
     *         @OA\JsonContent(
     *             type="string",
     *             description="Token JWT",
     *             example={"token": "eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJpc3MiOiJodHRwOlwvXC9sb2NhbGhvc3Q6ODAwMFwvYXBpXC9sb2dpbiIsImF1ZCI6Imh0dHA6XC9cL2xvY2FsaG9zdDo4MDAwXC9hcGkiLCJpYXQiOjE1ODc0NjQ3MjAsImV4cCI6MTU4NzQ2ODMyMH0.Dg4YTnlQnESWNzKs25dajb8_XMQdeAfkxMM62RjjlHE"}
     *         ),
     *     ),
     *     @OA\Response(
     *         response="401",
     *         description="Paramètres incorrectes",
     *         ref="#/components/responses/unauthorized"
     *     ),
     * )
     */
    public function login(Request $request, UserRepository $userRepository, AdminRepository $adminRepository): Response {
        if (($tagRFID = $request->request->get('tag_rfid')) && ($pinNumber = $request->request->get('pin_number'))) {
            $user = $userRepository->findOneBy(['tag_rfid' => $tagRFID]);
            if ($user) {
                $admin = $adminRepository->find($user->getId());
                if ($admin) {
                    if($admin->getPinTerminal() == $pinNumber) {
                        // Génération du token
                        $time = time();
                        $signer = new Sha256();
                        $token = (new Builder())->issuedBy($request->getUri())
                            ->permittedFor($request->getUriForPath('/api'))
                            ->issuedAt($time)
                            ->expiresAt($time + 3600)
                            ->getToken($signer, new Key($_ENV['JWT_SECRET']));
                        return new JsonResponse(['token' => (string)$token]);
                    } else {
                        $response = new JsonResponse(['code' => 'error', 'message' => "PIN incorrect"]);
                        $response->setStatusCode(Response::HTTP_BAD_REQUEST);
                        return $response;
                    }
                }
            }
            $response = new JsonResponse(['code' => 'error', 'message' => "Utilisateur introuvable"]);
            $response->setStatusCode(Response::HTTP_BAD_REQUEST);
            return $response;
        } else {
            $response = new JsonResponse(['code' => 'error', 'message' => "Paramètres incorrectes"]);
            $response->setStatusCode(Response::HTTP_BAD_REQUEST);
            return $response;
        }
    }

    /**
     * @Route("/debit", name="debit")
     * @param Request $request
     * @return Response
     */
    public function debit(Request $request): Response {
        if ($this->authorize($request)) {
            return $this->doTransaction($request, TransactionType::DEBIT);
        } else {
            $response = new JsonResponse(['status' => 'error', 'message' => "Accès non-autorisé"]);
            $response->setStatusCode(Response::HTTP_UNAUTHORIZED);
            return $response;
        }
    }

    /**
     * @Route("/credit", name="credit")
     * @param Request $request
     * @return Response
     * @throws Exception
     */
    public function credit(Request $request): Response {
        if ($this->authorize($request)) {
            return $this->doTransaction($request, TransactionType::CREDIT);
        } else {
            $response = new JsonResponse(['status' => 'error', 'message' => "Accès non-autorisé"]);
            $response->setStatusCode(Response::HTTP_UNAUTHORIZED);
            return $response;
        }
    }

    /**
     * @Route("/new-user", name="new_user", methods={"GET"})
     * @param Request $request
     * @return Response
     */
    public function newUser(Request $request): Response {
        if ($this->authorize($request)) {
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
        } else {
            $response = new JsonResponse(['status' => 'error', 'message' => "Accès non-autorisé"]);
            $response->setStatusCode(Response::HTTP_UNAUTHORIZED);
            return $response;
        }
    }

    private function authorize(Request $request): bool {
        $signer = new Sha256();
        $token = $request->headers->get('Authorization');
        $token = explode(' ', $token)[1];
        $token = (new Parser())->parse((string)$token);
        // TODO: Changer la clé secrète 'test'
        if ($token->verify($signer, 'test') && $token->hasClaim('exp') && $token->hasClaim('iss') && $token->hasClaim('aud')) {
            $data = new ValidationData();
            $data->setIssuer('http://localhost:8000/api/login');
            $data->setAudience('http://localhost:8000/api');
            return $token->validate($data);
        } else {
            return false;
        }
    }

    private function doTransaction(Request $request, int $type): Response {
        $entityManager = $this->getDoctrine()->getManager();
        $repository = $this->getDoctrine()->getRepository(User::class);

        $tagRfid = $request->query->get('tag_rfid');
        $amount = $request->query->get('amount');

        $admin = $this->getDoctrine()->getRepository(Admin::class)->findAll()[0];

        if (!empty($tagRfid) && !empty($amount) && is_numeric($amount)) {
            $user = $repository->findOneBy(['tag_rfid' => $tagRfid]);
            if ($user) {
                $transaction = new Transaction();
                if ($type === TransactionType::DEBIT)
                    $transaction->setAmount(-$amount);
                else if ($type === TransactionType::CREDIT)
                    $transaction->setAmount($amount);
                $transaction->setDate(new DateTime('now'));
                $transaction->setNumTerminal(1);
                $transaction->setUser($user);
                $transaction->setAdmin($admin);

                $entityManager->persist($transaction);
                $entityManager->flush();

                $response = new JsonResponse(['status' => 'success', 'message' => "Transaction effctuée avec succès"]);
                $response->setStatusCode(Response::HTTP_CREATED);
                return $response;
            } else {
                // User not found
                $response = new JsonResponse(['status' => 'error', 'message' => "L'utilisateur avec le tag RFID $tagRfid est introuvable"]);
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
