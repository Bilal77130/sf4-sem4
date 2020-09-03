<?php

namespace App\Controller;

use App\Entity\Event;
use App\Entity\Participant;
use App\Entity\Participation;
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
use App\Form\RegistrationFormType;
use App\Form\SendInvitationFormType;
use App\Repository\ParticipationRepository;
use App\Service\EmailSender;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Mime\Email;

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
     * List de tous les événements page front
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
     * Liste de tout les événements
     * @Route("/backoffice", name="app_backoffice")
     */
    public function gestionEvents(EventRepository $eventRepository, Request $request)
    {

        

        if( $this->security->getUser() ) 
        if (!in_array('ROLE_ADMIN', $this->security->getUser()->getRoles())) {
            return $this->redirectToRoute('access_denied');
         } else{
            return $this->redirectToRoute('access_denied');
         }



        $resultats = $eventRepository->findAll();

     
        return $this->render('backoffice/index.html.twig', [
            'controller_name' => 'HomeController',
            'events'=>$resultats,
            'post' => 'POST_EDIT'
        ]);
    }

    /**
     * se desinscrire 
     * @Route("/unsubscribe", name="app_unsubscribe_event")
     */
    public function unsubscribeEvent(ParticipationRepository $participationRepository, Request $request){


     



        $participation = $participationRepository->findByUserAndEvent(
                                                    $this->security->getUser(),
                                                    $request->get('idEvent') 
                                                );

        // if (!in_array('ROLE_ADMIN', $this->security->getUser()->getRoles())) {
        //     if (!$this->isGranted('POST_VIEW', $participation)) {
        //         return $this->redirectToRoute('access_denied');
        //     }
        // }  

        $this->entityManager->remove($participation);
        $this->entityManager->flush();
        $this->addFlash('danger','Vous n\'êtes plus inscrit.');
        return $this->redirectToRoute('event_page',array('id' => $request->get('idEvent')));
    }



    /**
     * @Route("/subscribEvent", name="app_subscribe_event")
     */
    public function subscribeEvent(EventRepository $eventRepository, Request $request, ParticipationRepository $participationRepository)
    {
        
        $event = $eventRepository->find($request->get('id'));
        $user = $this->security->getUser();
        
        $participant = new Participation();
        $participant->setUser($user);
        $participant->setEvent($event);

        $this->entityManager->persist($participant);
        $this->entityManager->flush();

        $this->addFlash('success','Vous êtes bien inscrit.');
     
        $participation = $participationRepository->findByUserAndEvent(
            $this->security->getUser(),
            $event 
        );

        return $this->render('home/event_page.html.twig', [
            'controller_name' => 'Description d\'un événement',
            'event'=>$event,
            'isParticipate'=> (!is_null($participation)) ? true : false
           
        ]);

        
    }



    /**
     * @Route("/eventsCreate", name="list_envents_user_created")
     */
    public function list_envent_user_created(EventRepository $repository)
    {
        if($this->security->getUser()){
        $idUser = $this->security->getUser();
        $eventCreated = $repository->findByUser($idUser);
        }else{
            $eventCreated = array();
        }

        return $this->render('home/page_events_user_created.html.twig', [
            'controller_name' => 'Liste des evenements crées',
            'list_event_created'=>$eventCreated
        ]);
    }
    /**
     * @Route("/eventsPaticipate", name="list_envents_user_participated")
     */
    public function list_envent_user_participated(EventRepository $eventRepository,ParticipationRepository $ParticipantionRepository)
    {


        $eventParticipated= $ParticipantionRepository->findByUser($this->security->getUser());

        return $this->render('home/page_events_user_paticipated.html.twig', [
            'controller_name' => 'Liste des evenements où je participe',
            'list_participation'=>$eventParticipated
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
    public function event_page(Request $request, EventRepository $repository, ParticipationRepository $participationRepository)
    {


        $event = $repository->find($request->get('id'));

        $participation = $participationRepository->findByUserAndEvent(
            $this->security->getUser(),
            $event 
        );

        // dd($participation);

        return $this->render('home/event_page.html.twig', [
            'controller_name' => 'Description d\'un événement',
            'event'=>$event,
            'isParticipate'=> (!is_null($participation)) ? true : false
        ]);
    }


    /**
     * @Route("/event/{id}/edit", name="modif_event")
     */
    public function modifEvent(Request $request, EventRepository $repository, ParticipationRepository $participationRepository)
    {

        

        $event = $repository->find($request->get('id'));

        if( $this->security->getUser() ) 
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
            return $this->redirectToRoute('event_page',array('id' => $event->getId()));
        
        }


        $participation = $participationRepository->findByUserAndEvent(
            $this->security->getUser(),
            $event 
        );

        return $this->render('home/event_modif.html.twig', [
            'controller_name' => 'Description d\'un événement',
            'event'=>$event,
            'modif_event_form'=>$form->createView(),
            'isParticipate'=> (!is_null($participation)) ? true : false
        ]);
    }
    /**
     * Suppresion d'un produit
     * @Route("/access/event/{id}/delete", name="delete_event")
     */
    public function suppression(Request $request, EventRepository $repository,ParticipationRepository $participationRepository){

        $event = $repository->find($request->get('id'));
        // on supprimer d'abord les depandances
        $participation = $participationRepository->findByUserAndEvent(
            $this->security->getUser(),
            $event 
        );

        // dd($participation);
        // dd($event);

        if(!is_null($participation)){
            $this->entityManager->remove($participation);
            $this->entityManager->flush();
        }

        // dd($event);

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

/**
     * @Route("/sendMail", name="app_send_email")
     */
    public function sendEmail(Request $request, EmailSender $emailSender, EventRepository $eventRepository ): Response
    {
        $form = $this->createForm(SendInvitationFormType::class);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // Récupération des données de formulaire (entité User + mot de passe)
            $res = ($form->getData());
           
            $event = $res['eventName'];
            // dd($event);
            $aEmail = ($form->getData());


           $emailToSend = $aEmail['email'];

         
            // Envoi de l'email de confirmation
            $emailSender->sendInvitationEvent($emailToSend,$event);

            $this->addFlash('success', 'Votre invitation est envoyée ! Un email de confirmation lui a été envoyé.');
            return $this->redirectToRoute('app_send_email');
        }

        return $this->render('event/send_invitation.html.twig', [
            'registrationForm' => $form->createView(),
        ]);
    }
    

}
