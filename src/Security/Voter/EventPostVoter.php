<?php

namespace App\Security\Voter;

use App\Entity\Event;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;
use Symfony\Component\Security\Core\User\UserInterface;

class EventPostVoter extends Voter
{
    protected function supports($attribute, $subject)
    {
        // replace with your own logic
        // https://symfony.com/doc/current/security/voters.html

        // return $attribute === 'NOTE_DELETE'
        // && $subject instanceof Event;

        return in_array($attribute, ['POST_EDIT', 'POST_VIEW','ROLE_ADMIN'])
            && $subject instanceof \App\Entity\Event;
    }

    protected function voteOnAttribute($attribute, $subject, TokenInterface $token)
    {
      

        $user = $token->getUser();
        // if the user is anonymous, do not grant access
        if (!$user instanceof UserInterface) {
            return false;
        }

        // Administrateur = autorisé à supprimer toutes les notes
        if (in_array('ROLE_ADMIN', $user->getRoles())) {
            return true;
        }



        // ... (check conditions and return true to grant permission) ...
        switch ($attribute) {
            case 'POST_EDIT':
                // logic to determine if the user can EDIT
                if($user->getId()==$subject->getAuthor()->getId() ){
                    // dd('ici');
                    return true;
                }
                // return true or false
                
                break;
            case 'POST_VIEW':
                // logic to determine if the user can VIEW
                // return true or false
                return true;
                break;
        }

        return false;
    }

     /**
     * @Route("/posts/{id}", name="post_show")
     */
    public function show($id)
    {
        // get a Post object - e.g. query for it
        
        $post = new \App\Entity\Event;

        // check for "view" access: calls all voters
        $this->denyAccessUnlessGranted('view', $post);

        // ...
    }

    /**
     * @Route("/posts/{id}/edit", name="post_edit")
     */
    public function edit($id)
    {
        // get a Post object - e.g. query for it
        $post = new \App\Entity\Event;

        // check for "edit" access: calls all voters
        $this->denyAccessUnlessGranted('edit', $post);

        // ...
    }
}
