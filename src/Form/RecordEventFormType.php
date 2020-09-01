<?php

namespace App\Form;

use App\Entity\Event;
use App\Entity\User;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Email;
use Symfony\Component\Validator\Constraints\IsTrue;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\Regex;
use Symfony\Component\Form\FormTypeInterface;
class RecordEventFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder

        ->add('author', EntityType::class, [
            'class' => User::class,
            'choice_label' => 'username',
            'label'=>false
        ])

        ->add('name',TextType::class,[
            'attr' => ['placeholder' => 'Veuillez entrez un nom'],
                'constraints'=>[
                    new NotBlank(['message'=>'Pseudo manquant']),
                    new Length([
                        'max'=>30,
                        'maxMessage'=>'Le nom contenir plus de 180 caractère',
                        'min'=>3,
                        'minMessage'=>'Le nom doit contenir au moins de 3 caractère',
                        
                    ])
                ]
            ])
            

            ->add('description',TextType::class,[
                'attr' => ['placeholder' => 'Veuillez entrez une description'],
                'required'=>false,
                'constraints'=>[
                    new Length([
                        'max'=>180,
                        'maxMessage'=>'La description ne peut contenir plus de 180 caractère',
                        'min'=>3,
                        'minMessage'=>'La description ne peut contenir au moins de 3 caractère',
                        
                    ])
                ]
            ])
          ->add('place',TextType::class,[
            'attr' => ['placeholder' => 'Veuillez entrez le lieu de l\'événement'],
                'constraints'=>[
                    new NotBlank(['message'=>'Veuillez entrer un lieu']),
                    new Length([
                        'max'=>30,
                        'maxMessage'=>'Le pseudo ne peut contenir plus de 180 caractère',
                        'min'=>3,
                        'minMessage'=>'Le pseudo ne peut contenir au moins de 3 caractère',
                        
                    ])
                ]
            ])


            ->add('eventDate',DateTimeType::class,[
                'attr' => ['placeholder' => 'Veuillez entrez le lieu une date'],
                    // 'constraints'=>[
                    //     new NotBlank(['message'=>'Veuillez entrer un lieu']),
                    //     new Length([
                    //         'max'=>30,
                    //         'maxMessage'=>'Le pseudo ne peut contenir plus de 180 caractère',
                    //         'min'=>3,
                    //         'minMessage'=>'Le pseudo ne peut contenir au moins de 3 caractère',
                            
                    //     ])
                    // ]
                ])
         ;

       
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Event::class,
            // 'data_class' => User::class,
        ]);
    }
}
