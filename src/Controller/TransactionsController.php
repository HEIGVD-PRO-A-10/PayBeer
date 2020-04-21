<?php


namespace App\Controller;


use App\Repository\TransactionRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class TransactionsController extends AbstractController {

    /**
     * @Route("/history", name="history")
     */
    public function history(Request $request, TransactionRepository $transactionRepository) {
        if($query = $request->query->get('q')) {
            //$transactions = $transactionRepository->searchByLastnameOrFirstname($query);
            return $this->render('transactions/history.html.twig', ['transactions' => [], 'query' => $query]);
        } else {
            $transactions = $transactionRepository->findAllSorted();
            return $this->render('transactions/history.html.twig', ['transactions' => $transactions]);
        }
    }

    /**
     * @Route("/transaction/{id}", name="transaction_info")
     * @param $id
     * @param TransactionRepository $repo
     * @return Response
     */
    public function info($id, TransactionRepository $repo) {
        $transaction = $repo->find($id);
        if($transaction) {
            return $this->render('transactions/info.html.twig', ['transaction' => $transaction]);
        } else {
            return $this->render('404.html.twig');
        }
    }

    /**
     * @Route("/transaction/{id}/cancel", name="transaction_cancel")
     * @param $id
     * @param Request $request
     * @param TransactionRepository $repo
     * @return Response
     */
    public function cancel($id, Request $request, TransactionRepository $repo) {
        $manager = $this->getDoctrine()->getManager();
        $transaction = $repo->find($id);
        if($transaction) {
            $transaction->setStatus('CANCELED');
            $manager->flush();
            $referer = $request->headers->get('referer');
            if($referer) {
                return $this->redirect($referer);
            } else {
                return $this->redirectToRoute('transaction_info', ['id' => $id]);
            }
        } else {
            return $this->render('404.html.twig');
        }
    }

    /**
     * @Route("/transaction/{id}/maintain", name="transaction_maintain")
     * @param $id
     * @param Request $request
     * @param TransactionRepository $repo
     * @return Response
     */
    public function maintain($id, Request $request, TransactionRepository $repo) {
        $manager = $this->getDoctrine()->getManager();
        $transaction = $repo->find($id);
        if($transaction) {
            $transaction->setStatus(null);
            $manager->flush();
            $referer = $request->headers->get('referer');
            if($referer) {
                return $this->redirect($referer);
            } else {
                return $this->redirectToRoute('transaction_info', ['id' => $id]);
            }
        } else {
            return $this->render('404.html.twig');
        }
    }

}
