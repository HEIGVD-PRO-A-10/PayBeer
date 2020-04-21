<?php

use OpenApi\Annotations as OA;

/**
 * @OA\Info(
 *     title="PayBeer API",
 *     version="0.1",
 *     description="API pour le système de prépaiement du ChillOut",
 * )
 *
 * @OA\Server(
 *     url="https://paybeer.artefactori.ch/api",
 *     description="Serveur principal de pré-production",
 * )
 *
 * @OA\Tag(
 *     name="paybeer",
 *     description="Routes de base pour la communication avec les terminaux de paiement"
 * )
 *
 *
 * @OA\SecurityScheme(
 *     type="http",
 *     scheme="bearer",
 *     bearerFormat="JWT",
 *     securityScheme="jwt",
 *     description="Les JSON Web Tokens sont utilisés pour authoriser l'API. On peut en récupéré un grâce à la route /login."
 * )
 *
 * @OA\Response(
 *     response="unauthorized",
 *     description="Non-authorisé",
 *     @OA\JsonContent(ref="#/components/schemas/Error")
 * )
 *
 * @OA\Response(
 *     response="bad_request",
 *     description="Paramètres incorrects",
 *     @OA\JsonContent(ref="#/components/schemas/Error")
 * )
 *
 * @OA\Schema(
 *     type="object",
 *     schema="Error",
 *     @OA\Property(property="code", type="string"),
 *     @OA\Property(property="message", type="string"),
 *     required={"code", "message"}
 * )
 */
