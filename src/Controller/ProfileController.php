<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\ModificationPasswordFormType;
use App\Form\ModificationUserDataFormType;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class ProfileController extends AbstractController
{

    private $passwordEncoder;

         public function __construct(UserPasswordEncoderInterface $passwordEncoder)
         {
             $this->passwordEncoder = $passwordEncoder;
         }

    /**
     * @Route("/profile", name="app_profile")
     */
    public function index(Request $request,EntityManagerInterface $em,UserPasswordEncoderInterface $encoder)
    {
        $user = new User();
        $user = $em->find(User::class,$this->getUser());

      

        $form = $this->createForm(ModificationUserDataFormType::class,$user);
        $form->handleRequest($request);
        if($form->isSubmitted() && $form->isValid()){
            // dd($user);
            $em->flush();
            $this->addFlash('success','L\'utilisateur à été mise à jour.');
 
             $em->persist($user);
             $em->flush();
            
            
        }

        $formPassword = $this->createForm(ModificationPasswordFormType::class,$user);
        $formPassword->handleRequest($request);
        if($formPassword->isSubmitted() && $formPassword->isValid()){
            
            $res = $request->get('modification_password_form');
            $newPassword = $res['plainPassword']['first'];

            $encoded = $encoder->encodePassword($user, $newPassword);

            $user->setPassword($encoded);

            $em->flush();
            $this->addFlash('success','Le mot de passe à été mise à jour.');   
            
        }
        

        return $this->render('profile/index.html.twig', [
            'user'=>$user,
            'user_form'=>$form->createView(),
            'password_form'=>$formPassword->createView(),
            'form_action'=>'update_user',
            'form_action2'=>'update_password'
        ]);
    }

   

   /**
     * Modification d'un user
     * @Route("/user/{id}", name="modif_user")
     */

    public function modification(User $user, Request $request, EntityManagerInterface $em){

        $form = $this->createForm(ModificationUserDataFormType::class,$user);
        $form->handleRequest($request);
        if($form->isSubmitted() && $form->isValid()){
            $em->flush();
            $this->addFlash('success','L\'utilisateur à été mise à jour.');
            
        }

        if($form->isSubmitted() && $form->isValid()){     
            $res = $request->get('modification_password_form');
            $newPassword = $res['plainPassword']['first'];
 
             $em->persist($user);
             $em->flush();
             $this->addFlash('success','Le mot de passe à bien été mise à jour.');   
         }

        return $this->render('profile/modif_form_user.html.twig',[
            'user'=>$user,
            'user_form'=>$form->createView(),
            'form_action'=>'update_user'
        ]);
    }
/**
     * Modification d'un user
     * @Route("/modifPassword/{id}", name="modif_password")
     */

    public function modificationPassword(User $user, Request $request, EntityManagerInterface $em){

        $form = $this->createForm(ModificationPasswordFormType::class,$user);
        $form->handleRequest($request);
        if($form->isSubmitted() && $form->isValid()){     
           $res = $request->get('modification_password_form');
           $newPassword = $res['plainPassword']['first'];

           

            $em->persist($user);
            $em->flush();
            $this->addFlash('success','L\'utilisateur à été mise à jour.');   
        }

        return $this->render('profile/modif_form_user.html.twig',[
            'user'=>$user,
            'user_form'=>$form->createView(),
            'password_form'=>$form->createView(),
            'form_action'=>'update_password'
        ]);
    }
}
