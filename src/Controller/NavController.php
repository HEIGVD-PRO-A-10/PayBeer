<?php


namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;


class NavController extends AbstractController {

    public function showNav($currentPage) {


        //TODO: REMPLACE CE TABLEAU
        $pages = array(
            array(
                'name' => 'add_user',
                'desc' => 'Ajouter utilisateur'
            ),
            array(
                'name' => 'history',
                'desc' => 'Historique'
            ),
            array(
                'name' => 'list_user',
                'desc' => 'Lister utilisateur'
            )
        );

        $data =  array(
            'current_page' => $currentPage,
            'pages' => $pages
        );

        return $this->render('navigation.html.twig', $data);
    }
}