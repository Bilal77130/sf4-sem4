<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

class HomeController extends AbstractController
{
    /**
     * @Route("/", name="home")
     */
    public function index()
    {
        return $this->render('home/index.html.twig', [
            'controller_name' => 'HomeController',
        ]);
    }

    /**
     * @Route("/", name="list_envents_user_created")
     */
    public function list_envent_user_created()
    {
        return $this->render('home/page_events_user_created.html.twig', [
            'controller_name' => 'Liste des evenements des utilisateurs',
        ]);
    }
    /**
     * @Route("/", name="list_envents_user_participated")
     */
    public function list_envent_user_participated()
    {
        return $this->render('home/page_events_user_paticipated.html.twig', [
            'controller_name' => 'Liste des evenements des utilisateurs',
        ]);
    }
}
