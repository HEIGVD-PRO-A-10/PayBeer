<?php


namespace App\Controller;


use App\Repository\TransactionRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class TransactionsController extends AbstractController {

    /**
     * @Route("/", name="home")
     */
    public function home() {
        $data =  array('current_page' => 'home', "theme" => "red");
        return $this->render('home.html.twig', $data);
    }

    /**
     * @Route("/history", name="history")
     */
    public function history(Request $request, TransactionRepository $transactionRepository) {
        if($query = $request->query->get('q')) {
            //$transactions = $transactionRepository->searchByLastnameOrFirstname($query);
            return $this->render('history.html.twig', ['transactions' => [], 'query' => $query]);
        } else {
            $transactions = $transactionRepository->findAll();
            return $this->render('history.html.twig', ['transactions' => $transactions]);
        }
    }

}
