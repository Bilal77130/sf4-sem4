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

    // to do gerer les droits d'accès avec is_granted en php

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

       
        // only admin roles can access to back-office
        if( $this->security->getUser() ){
            if (!in_array('ROLE_ADMIN', $this->security->getUser()->getRoles())) {
                return $this->redirectToRoute('access_denied');
            } 
        }elseif(is_null($this->security->getUser() )){
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
     * @Route("/sendMail", name="app_send_email")
     */
    public function sendEmail(Request $request, EmailSender $emailSender, EventRepository $eventRepository ): Response
    {
        $form = $this->createForm(SendInvitationFormType::class);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
             $res = ($form->getData());      
            $event = $res['eventName'];
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
