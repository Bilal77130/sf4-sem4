<?php

namespace App\Controller;

use App\Entity\Event;
use App\Form\RecordEventFormType;
use App\Form\SendInvitationFormType;
use App\Repository\EventRepository;
use App\Service\EmailSender;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Security;

class EventController extends AbstractController
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
     * @Route("/event", name="event")
     */
    public function index()
    {
        return $this->render('event/index.html.twig', [
            'controller_name' => 'EventController',
        ]);
    }

      /**
     * @Route("/event/created", name="list_envents_user_created")
     */
    public function list_envent_user_created(EventRepository $repository)
    {
        if($this->security->getUser()){
        $idUser = $this->security->getUser();
        $eventCreated = $repository->findByUser($idUser);
        }else{
            $eventCreated = array();
        }

        return $this->render('event/page_events_user_created.html.twig', [
            'controller_name' => 'Liste des evenements crées',
            'list_event_created'=>$eventCreated
        ]);
    }

/**
     * @Route("/event/paticipate", name="list_envents_user_participated")
     */
    public function list_envent_user_participated(EventRepository $eventRepository)
    {

        // dd($this->security->getUser()->getParticipation());

        if($this->security->getUser())
            $eventParticipated = $this->security->getUser()->getParticipation();
        else
            $eventParticipated = [];


        // $eventParticipated= $ParticipantionRepository->findByUser($this->security->getUser());

        return $this->render('event/page_events_user_paticipated.html.twig', [
            'controller_name' => 'Liste des evenements où je participe',
            'list_participation'=>$eventParticipated
        ]);
    }

   /**
     * @Route("/event/add", name="add_event")
     */
    public function addEvent(Request $request)
    {
        $event = new Event();
        $user = $this->security->getUser();
        $event->setAuthor( $user);

        $form = $this->createForm(RecordEventFormType::class,$event);
        $form->handleRequest($request);
        if($form->isSubmitted() && $form->isValid()){
            $this->addFlash('success','L\'événement a bien été ajouté.');
            $this->entityManager->persist($event);
            $this->entityManager->flush();
        
        }


        return $this->render('event/add_event.html.twig', [
            'controller_name' => 'Liste des evenements des utilisateurs',
            'form_action'=>'add_event',
            'record_event_form'=>$form->createView()
        ]);
    }
     /**
     * @Route("/event/{id}", name="event_page")
     */
    public function event_page(Request $request, EventRepository $repository, EmailSender $emailSender)
    {

        $event = $repository->find($request->get('id'));
        $aParticipants = $event->getParticipants();
        $isParticipant  = $aParticipants->contains($this->security->getUser());
  

        $form = $this->createForm(SendInvitationFormType::class);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
                  
            $aEmail = ($form->getData());
            $emailToSend = $aEmail['email'];
  
            // Envoi de l'email de confirmation
            $emailSender->sendInvitationEvent($emailToSend,$event);

            $this->addFlash('success', 'Votre invitation est envoyée ! Un email de confirmation lui a été envoyé.');
            // return $this->redirectToRoute('app_send_email');
        }






        return $this->render('event/event_page.html.twig', [
            'controller_name' => 'Description d\'un événement',
            'event'=>$event,
            'isParticipate'=> $isParticipant,
            'registrationForm' => $form->createView(),
        ]);
    }


    /**
     * @Route("/event/{id}/edit", name="modif_event")
     */
    public function modifEvent(Request $request, EventRepository $repository)
    {

        

        $event = $repository->find($request->get('id'));
        

        if( $this->security->getUser() ){ 
            if (!in_array('ROLE_ADMIN', $this->security->getUser()->getRoles())) {
                if (!$this->isGranted('POST_EDIT', $event)) {
                    return $this->redirectToRoute('access_denied');
                }
            }   
        }else{
            return $this->redirectToRoute('access_denied');
        }
        $form = $this->createForm(RecordEventFormType::class,$event);
        $form->handleRequest($request);
        if($form->isSubmitted() && $form->isValid()){
            $this->addFlash('success','L\'événement a bien été modifé.');
            $this->entityManager->persist($event);
            $this->entityManager->flush();
            return $this->redirectToRoute('event_page',array('id' => $event->getId()));
        
        }

        // to do determin how is participated or not
        $aParticipants = $event->getParticipants();
        $isParticipant  = $aParticipants->contains($this->security->getUser());

        // dd($isParticipant);

        return $this->render('event/event_modif.html.twig', [
            'controller_name' => 'Description d\'un événement',
            'event'=>$event,
            'modif_event_form'=>$form->createView(),
            'isParticipate'=>$isParticipant
        ]);
    }
    /**
     * Suppresion d'un produit
     * @Route("/event/{id}/delete", name="delete_event")
     */
    public function suppression(Request $request, EventRepository $repository){

        $event = $repository->find($request->get('id'));

        if( $this->security->getUser() ){ 
            if (!in_array('ROLE_ADMIN', $this->security->getUser()->getRoles())) {
                if (!$this->isGranted('POST_EDIT', $event)) {
                    return $this->redirectToRoute('access_denied');
                }
            }   
        }else{
            return $this->redirectToRoute('access_denied');
        }

        if($event){
            $this->entityManager->remove($event);
            $this->entityManager->flush();
        }

        // dd($event);

        $this->addFlash('danger','L\'événement à bien été supprimé.');

        return $this->redirectToRoute('home');
    }

 /**
     * se desinscrire 
     * @Route("/unsubscribe", name="app_unsubscribe_event")
     */
    public function unsubscribeEvent(EventRepository $eventRepository, Request $request){
  

        // if (!in_array('ROLE_ADMIN', $this->security->getUser()->getRoles())) {
        //     if (!$this->isGranted('POST_VIEW', $participation)) {
        //         return $this->redirectToRoute('access_denied');
        //     }
        // }
        $event =$eventRepository->find($request->get('idEvent') );

        
        $user = $event->removeParticipant($this->security->getUser());
        $this->entityManager->persist($user);
        $this->entityManager->flush();
        $this->addFlash('danger','Vous n\'êtes plus inscrit.');
        return $this->redirectToRoute('event_page',array('id' => $request->get('idEvent')));
    }



    /**
     * @Route("/event/{id}/subscribe", name="app_subscribe_event")
     */
    public function subscribeEvent(EventRepository $eventRepository, Request $request)
    {
        
        $event = $eventRepository->find($request->get('id'));
        $user = $this->security->getUser();
        
        $user->addParticipation($event);

        $this->entityManager->persist($user);
        $this->entityManager->flush();

        $this->addFlash('success','Vous êtes bien inscrit.');
     
        $aParticipants = $event->getParticipants();
        $isParticipant  = $aParticipants->contains($user);


        return $this->render('event/event_page.html.twig', [
            'controller_name' => 'Description d\'un événement',
            'event'=>$event,
            'isParticipate'=> $isParticipant
           
        ]);

        
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
