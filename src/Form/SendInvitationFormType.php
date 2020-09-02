<?php

namespace App\Form;

use App\Entity\Event;
use App\Entity\User;
use App\Repository\EventRepository;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\Validator\Constraints\IsTrue;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;

class SendInvitationFormType extends AbstractType
{

    /**
     * @var Security
     */
    private $security;
    public function __construct(Security $security, EntityManagerInterface $em,  EventRepository $eventRepository)
    {
       $this->security = $security;
       $this->entityManager=$em;
       $this->eventRepository = $eventRepository;
    }



    public function buildForm(FormBuilderInterface $builder, array $options)
    {


        // todo si pas connecté rediriger en amont

        $user = ($this->security->getUser());
        $findMyEvents = $this->eventRepository->findByUser($user);

        // dd($findMyEvents);


        $builder
            ->add('email')
            ->add('eventName', EntityType::class, [
                'class' => Event::class,
                // 'choice_label' => function ($category) {
                    // return $category->getDisplayName();
                // }
                'choice_label' => 'name',
            ])

            // ->add('eventChoice',HiddenType::class,[

            // ])
        ;
    }

    // public function configureOptions(OptionsResolver $resolver)
    // {
    //     $resolver->setDefaults([
    //         'data_class' => Event::class,
    //     ]);
    // }
}
