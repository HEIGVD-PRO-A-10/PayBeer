<?php


namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;


class HistoryController extends AbstractController {

    /**
     * @Route("/history", name="history")
     */
    public function history() {


        $data =  array('current_page' => 'history', "color" => "red");
        return $this->render('history.html.twig', $data);
    }

}