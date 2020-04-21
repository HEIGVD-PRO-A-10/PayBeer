<?php

namespace App\Controller;

use App\Repository\AdminRepository;
use App\Repository\TransactionRepository;
use App\Repository\UserRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class HomeController extends AbstractController
{
    /**
     * @Route("/", name="home")
     * @param TransactionRepository $transactionRepository
     * @param UserRepository $userRepository
     * @param AdminRepository $adminRepository
     * @return Response
     */
    public function index(TransactionRepository $transactionRepository, UserRepository $userRepository, AdminRepository $adminRepository)
    {
        $transactions = $transactionRepository->findBy([], ['date' => 'DESC']);
        $balance = 0;
        foreach ($transactions as $transaction) {
            $balance += $transaction->getAmount();
        }
        $users = $userRepository->findBy([], ['created_at' => 'DESC']);
        $blockedUsers = $userRepository->findBy(['status' => 'BLOCKED'], ['created_at' => 'DESC']);
        $overdraftUsers = $userRepository->findAllOverdraft();
        $admins = $adminRepository->findAll();

        return $this->render('home.html.twig', [
            'balance' => $balance,
            'blockedUsers' => array_slice($blockedUsers, 0, 10),
            'overdraftUsers' => array_slice($overdraftUsers, 0, 5),
            'transactions' => array_slice($transactions, 0, 5),
            'lastUsers' => array_slice($users, 0, 5),
            'admins' => $admins,
            'rfid_test' => $admins[0]->getUser()->getTagRfid(),
            'pin_test' => $admins[0]->getPinTerminal(),
        ]);
    }
}
