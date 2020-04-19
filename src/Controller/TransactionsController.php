<?php


namespace App\Controller;


use App\Repository\TransactionRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class TransactionsController extends AbstractController {

    /**
     * @Route("/history", name="history")
     */
    public function history(Request $request, TransactionRepository $transactionRepository) {
        if($query = $request->query->get('q')) {
            //$transactions = $transactionRepository->searchByLastnameOrFirstname($query);
            return $this->render('history.html.twig', ['transactions' => [], 'query' => $query]);
        } else {
            $transactions = $transactionRepository->findBy([], ['date' => 'DESC']);
            return $this->render('history.html.twig', ['transactions' => $transactions]);
        }
    }

}
