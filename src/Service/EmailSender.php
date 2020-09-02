<?php

namespace App\Service;

use App\Entity\User;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Address;

/**
 * Service chargé de créer et d'envoyer des emails
 */
class EmailSender
{
    private $mailer;

    public function __construct(MailerInterface $mailer)
    {
        $this->mailer = $mailer;
    }

    /**
     * Créer un email préconfiguré
     * @param string $subject   Le sujet du mail
     */
    private function createTemplatedEmail(string $subject): TemplatedEmail
    {
        return (new TemplatedEmail())
            ->from(new Address('bilal.doranco@gmail.com', 'Bilal'))      # Expéditeur
            ->subject("\u{1F555} EventProject | $subject")                # Objet de l'email
        ;	
    }

    /**
     * Envoyer un email de confirmation de compte suite à l'inscription
     * @param User $user    l'utilisateur devant confirmer son compte
     */
    public function sendAccountConfirmationEmail(User $user): void
    {
        
        $email = $this->createTemplatedEmail('Confirmation du compte')
            ->to(new Address($user->getEmail(), ucfirst($user->getPseudo() )))    # Destinataire
            ->htmlTemplate('email/account_confirmation.html.twig')      # template twig du message
            ->context([                                                 # variables du template
                'user' => $user,
            ])
        ;

        // dd($user->getPseudo());
        // Envoi de l'email
        $this->mailer->send($email);
    }

 /**
     * Envoyer un email de confirmation de compte suite à l'inscription
     * @param User $user    l'utilisateur devant confirmer son compte
     */
    public function sendInvitationEvent($email,$event): void
    {
        // dd($event);
        
        $email = $this->createTemplatedEmail('Vous êtes invitez à l\'événement : '.$event->getName().'!')
            ->to(new Address($email, 'testeur'))    # Destinataire
            ->htmlTemplate('emails/invitation_event.html.twig')
            ->context([                             # variables du template
                'event' => $event,
            ])
            
            ;
            
            

        // dd($user->getPseudo());
        // Envoi de l'email
        $this->mailer->send($email);
    }



}