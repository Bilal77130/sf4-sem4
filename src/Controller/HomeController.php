<?php

namespace App\Controller;

use App\Entity\Event;
use App\Entity\User;
use App\Form\RecordEventFormType;
use App\Repository\EventRepository;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Security;
Use App\Form\PostType;


class HomeController extends AbstractController
{

     /**
     * @var Security
     */
    private $security;
    public function __construct(Security $security, EntityManagerInterface $em)
    {
       $this->security = $security;
       $this->entityManager=$em;
    }



    /**
     * @Route("/", name="home")
     */
    public function index(EventRepository $eventRepository)
    {

        $resultats = $eventRepository->findAll();

     
       

        return $this->render('home/index.html.twig', [
            'controller_name' => 'HomeController',
            'events'=>$resultats,
        ]);
    }

    /**
     * @Route("/backoffice", name="app_backoffice")
     */
    public function gestionEvents(EventRepository $eventRepository, Request $request)
    {
        $resultats = $eventRepository->findAll();


    
        // $test = $eventRepository->find($this->getUser()->getId()) !== $this->getUser();
        // dd($test);
     
        return $this->render('backoffice/index.html.twig', [
            'controller_name' => 'HomeController',
            'events'=>$resultats,
            'post' => 'POST_EDIT'
        ]);
    }




    /**
     * @Route("/eventsCreate", name="list_envents_user_created")
     */
    public function list_envent_user_created(EventRepository $repository)
    {
        $idUser = $this->security->getUser()->getId();
        $eventCreated = $repository->findByUser($idUser);

        // dd($test);

        return $this->render('home/page_events_user_created.html.twig', [
            'controller_name' => 'Liste des evenements crées',
            'list_event_created'=>$eventCreated
        ]);
    }
    /**
     * @Route("/eventsPaticipate", name="list_envents_user_participated")
     */
    public function list_envent_user_participated()
    {
        return $this->render('home/page_events_user_paticipated.html.twig', [
            'controller_name' => 'Liste des evenements où je participe',
        ]);
    }

    /**
     * @Route("/addEvent", name="add_event")
     */
    public function addEvent(Request $request)
    {
        $event = new Event();

        $user = $this->security->getUser();

        $test = $request;

        // dd($test);

        $event->setAuthor( $user);
  

        // dd($event);
                    

        $form = $this->createForm(RecordEventFormType::class,$event);
        $form->handleRequest($request);
        if($form->isSubmitted() && $form->isValid()){
            $this->addFlash('success','L\'événement a bien été ajouté.');
            $this->entityManager->persist($event);
            $this->entityManager->flush();
        
        }


        return $this->render('home/add_event.html.twig', [
            'controller_name' => 'Liste des evenements des utilisateurs',
            'form_action'=>'add_event',
            'record_event_form'=>$form->createView()
        ]);
    }


  

    /**
     * @Route("/event/{id}", name="event_page")
     */
    public function event_page(Request $request, EventRepository $repository)
    {
        $event = $repository->find($request->get('id'));
        return $this->render('home/event_page.html.twig', [
            'controller_name' => 'Description d\'un événement',
            'event'=>$event
        ]);
    }


    /**
     * @Route("/event/{id}/edit", name="modif_event")
     */
    public function modifEvent(Request $request, EventRepository $repository)
    {

        $event = $repository->find($request->get('id'));
        if (!in_array('ROLE_ADMIN', $this->security->getUser()->getRoles())) {
            if (!$this->isGranted('POST_EDIT', $event)) {
                return $this->redirectToRoute('access_denied');
            }
        }   
        $form = $this->createForm(RecordEventFormType::class,$event);
        $form->handleRequest($request);
        if($form->isSubmitted() && $form->isValid()){
            $this->addFlash('success','L\'événement a bien été modifé.');
            $this->entityManager->persist($event);
            $this->entityManager->flush();
        
        }


        return $this->render('home/event_modif.html.twig', [
            'controller_name' => 'Description d\'un événement',
            'event'=>$event,
            'modif_event_form'=>$form->createView()
        ]);
    }
    /**
     * Suppresion d'un produit
     * @Route("/access/denied", name="delete_event")
     */
    public function suppression(Request $request, EventRepository $repository){
        $event = $repository->find($request->get('id'));

        // dd($this->security->getUser());

        if (!in_array('ROLE_ADMIN', $this->security->getUser()->getRoles())) {
            if (!$this->isGranted('POST_EDIT', $event)) {
                return $this->redirectToRoute('access_denied');
            }
        }  
        $this->entityManager->remove($event);
        $this->entityManager->flush();
        $this->addFlash('danger','L\'événement à bien été supprimé.');

        return $this->redirectToRoute('app_backoffice');
    }

    /**
     * redirect to page denied
     * @Route("/denied", name="access_denied")
     */
    public function redirecToDenied()
    {
        return $this->render('home/denied.html.twig', [
            'controller_name' => 'Description d\'un événement',
          
        ]);
    }
}
