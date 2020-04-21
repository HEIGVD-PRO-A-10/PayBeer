<?php

namespace App\Controller\Api;

use App\Entity\Admin;
use App\Entity\Transaction;
use App\Entity\User;
use App\Repository\AdminRepository;
use App\Repository\UserRepository;
use DateTime;
use Exception;
use Lcobucci\JWT\Builder;
use Lcobucci\JWT\Parser;
use Lcobucci\JWT\Signer\Hmac\Sha256;
use Lcobucci\JWT\Signer\Key;
use Lcobucci\JWT\ValidationData;
use OpenApi\Annotations as OA;
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
class ApiController extends AbstractController {

    /**
     * @Route("/", name="docs")
     */
    public function index() {
        return $this->redirect('/api/index.html', Response::HTTP_MOVED_PERMANENTLY);
    }

    /**
     * @Route("/login", name="login", methods={"POST"})
     * @param Request $request
     * @param UserRepository $userRepository
     * @param AdminRepository $adminRepository
     * @return Response
     *
     * @OA\Post(
     *     path="/login",
     *     summary="Authentification à l'API",
     *     description="Il est nécessaire d'appeler cette route afin de s'authentifier à l'API. Celle-ci renvoie un token JWT en cas de succès. Ce token est valable 1 heure par défaut.",
     *     tags={"paybeer"},
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
     *             type="object",
     *             @OA\Property(property="token", type="string", example="eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJpc3MiOiJodHRwOlwvXC9sb2NhbGhvc3Q6ODAwMFwvYXBpXC9sb2dpbiIsImF1ZCI6Imh0dHA6XC9cL2xvY2FsaG9zdDo4MDAwXC9hcGkiLCJpYXQiOjE1ODc0NjQ3MjAsImV4cCI6MTU4NzQ2ODMyMH0.Dg4YTnlQnESWNzKs25dajb8_XMQdeAfkxMM62RjjlHE"),
     *         ),
     *     ),
     *     @OA\Response(
     *         response="401",
     *         ref="#/components/responses/unauthorized"
     *     ),
     *     @OA\Response(
     *         response="400",
     *         ref="#/components/responses/bad_request"
     *     ),
     * )
     */
    public function login(Request $request, UserRepository $userRepository, AdminRepository $adminRepository): Response {
        if (($tagRFID = $request->request->get('tag_rfid')) && ($pinNumber = $request->request->get('pin_number'))) {
            $user = $userRepository->findOneBy(['tag_rfid' => $tagRFID]);
            if ($user) {
                $admin = $adminRepository->find($user->getId());
                if ($admin) {
                    if ($admin->getPinTerminal() == $pinNumber) {
                        // Génération du token
                        $time = time();
                        $signer = new Sha256();
                        $token = (new Builder())->issuedBy($request->getUri())
                            ->permittedFor($request->getUriForPath('/api'))
                            ->issuedAt($time)
                            ->expiresAt($time + 3600)
                            ->withClaim('admin_id', $admin->getUser()->getId())
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
     * @Route("/transaction", name="transaction")
     * @param Request $request
     * @param UserRepository $userRepository
     * @param AdminRepository $adminRepository
     * @return Response
     *
     * @OA\Post(
     *     path="/transaction",
     *     summary="Ajoute une nouvelle transaction",
     *     description="Cette transaction peut être un débit ou bien un crédit selon le signe de la valeur.",
     *     tags={"paybeer"},
     *     @OA\RequestBody(
     *         @OA\MediaType(
     *             mediaType="application/x-www-form-urlencoded",
     *             @OA\Schema(
     *                 type="object",
     *                 @OA\Property(property="tag_rfid", type="string", description="Tag RFID", example="123456"),
     *                 @OA\Property(property="amount", type="integer", description="Montant de la transaction (positif ou négatif)", example="15"),
     *                 @OA\Property(property="num_terminal", type="integer", description="Numéro du terminal utilisé pour effectuer la transaction", default="1", example="1"),
     *                 @OA\Property(property="admin_id", type="integer", description="Identifiant de l'administrateur ayant effectué la transaction", example="123"),
     *                 required={"tag_rfid", "amount", "num_terminal", "admin_id"},
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response="201",
     *         description="Transaction créée"
     *     ),
     *     @OA\Response(
     *         response="401",
     *         ref="#/components/responses/unauthorized"
     *     ),
     *     @OA\Response(
     *         response="400",
     *         ref="#/components/responses/bad_request"
     *     ),
     *     security={{"jwt": {}}}
     * )
     */
    public function transaction(Request $request, UserRepository $userRepository, AdminRepository $adminRepository): Response {
        if ($adminId = $this->authorize($request)) {
            return $this->doTransaction($request, $adminId, $userRepository, $adminRepository);
        } else {
            $response = new JsonResponse(['code' => 'error', 'message' => "Accès non-autorisé"]);
            $response->setStatusCode(Response::HTTP_UNAUTHORIZED);
            return $response;
        }
    }

    /**
     * @Route("/new-user", name="new_user", methods={"POST"})
     * @param Request $request
     * @return Response
     *
     * @OA\Post(
     *     path="/new-user",
     *     summary="Ajoute un nouvel utilisateur",
     *     description="Si le tag RFID existe déjà en base de donnée, dans ce cas l'API renvoie une erreur de type 400",
     *     tags={"paybeer"},
     *     @OA\RequestBody(
     *         @OA\MediaType(
     *             mediaType="application/x-www-form-urlencoded",
     *             @OA\Schema(
     *                 type="object",
     *                 @OA\Property(property="tag_rfid", type="string", description="Tag RFID", example="123456"),
     *                 required={"tag_rfid"},
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response="201",
     *         description="Utilisateur ajouté"
     *     ),
     *     @OA\Response(
     *         response="401",
     *         ref="#/components/responses/unauthorized"
     *     ),
     *     @OA\Response(
     *         response="400",
     *         description="Si le tag RFID existe déjà en base de donnée ou que les paramètres sont incorrects",
     *         @OA\JsonContent(ref="#/components/schemas/Error")
     *     ),
     *     security={{"jwt": {}}}
     * )
     */
    public function newUser(Request $request): Response {
        if ($this->authorize($request)) {
            $entityManager = $this->getDoctrine()->getManager();
            $repository = $this->getDoctrine()->getRepository(User::class);

            $tagRfid = $request->request->get('tag_rfid');

            if (!empty($tagRfid)) {
                $existingUser = $repository->findOneBy(['tag_rfid' => $tagRfid]);
                if (!$existingUser) {
                    $user = new User();
                    $user
                        ->setTagRfid($tagRfid)
                        ->setStatus('NEW');

                    $entityManager->persist($user);
                    $entityManager->flush();

                    $response = new JsonResponse(['code' => 'success', 'message' => "L'utilisateur $tagRfid a bien été enregistré."]);
                    $response->setStatusCode(Response::HTTP_CREATED);
                    return $response;
                } else {
                    $response = new JsonResponse(['code' => 'error', 'message' => "Le tag RFID est déjà utilisé"]);
                    $response->setStatusCode(Response::HTTP_BAD_REQUEST);
                    return $response;
                }
            } else {
                $response = new JsonResponse(['code' => 'error', 'message' => "Paramètres incorrectes"]);
                $response->setStatusCode(Response::HTTP_BAD_REQUEST);
                return $response;
            }
        } else {
            $response = new JsonResponse(['code' => 'error', 'message' => "Accès non-autorisé"]);
            $response->setStatusCode(Response::HTTP_UNAUTHORIZED);
            return $response;
        }
    }

    private function authorize(Request $request) {
        $signer = new Sha256();
        $token = $request->headers->get('Authorization');
        $token = explode(' ', $token);
        if (isset($token[1])) {
            $token = $token[1];
            try {
                $token = (new Parser())->parse((string)$token);
                if (
                    $token->verify($signer, $_ENV['JWT_SECRET']) &&
                    $token->hasClaim('exp') &&
                    $token->hasClaim('iss') &&
                    $token->hasClaim('aud') &&
                    $token->hasClaim('admin_id')
                ) {
                    $data = new ValidationData();
                    $data->setIssuer($request->getUriForPath('/api/login'));
                    $data->setAudience($request->getUriForPath('/api'));
                    if($token->validate($data)) {
                        return $token->getClaim('admin_id');
                    } else {
                        return false;
                    }
                }
            } catch (Exception $e) {
                return false;
            }
        }
        return false;
    }

    private function doTransaction(Request $request, int $adminId, UserRepository $userRepository, AdminRepository $adminRepository): Response {
        $entityManager = $this->getDoctrine()->getManager();

        $tagRfid = $request->request->get('tag_rfid');
        $amount = $request->request->get('amount');

        $admin = $adminRepository->find($adminId);

        if (!empty($tagRfid) && !empty($amount) && is_numeric($amount) && $admin) {
            $user = $userRepository->findOneBy(['tag_rfid' => $tagRfid]);
            if ($user) {
                if($user->getStatus() === 'ACTIVE') {
                    $transaction = new Transaction();
                    $transaction->setAmount($amount);
                    $transaction->setDate(new DateTime());
                    $transaction->setNumTerminal(1);
                    $transaction->setUser($user);
                    $transaction->setAdmin($admin);

                    $entityManager->persist($transaction);
                    $entityManager->flush();

                    $response = new JsonResponse(['code' => 'success', 'message' => "Transaction effctuée avec succès"]);
                    $response->setStatusCode(Response::HTTP_CREATED);
                    return $response;
                } else {
                    $message = "L'utilisateur avec le tag RFID $tagRfid n'est pas actif";
                }
            } else {
                $message = "L'utilisateur avec le tag RFID $tagRfid est introuvable";
            }
        } else {
            $message = "Paramètres incorrectes";
        }
        $response = new JsonResponse(['code' => 'error', 'message' => $message]);
        $response->setStatusCode(Response::HTTP_BAD_REQUEST);
        return $response;
    }
}
