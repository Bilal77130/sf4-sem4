<?php

namespace App\Form;

use App\Entity\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
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

class ModificationUserDataFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('email', EmailType::class,[
                'constraints' => [
                    new NotBlank(['message'=>'Email manquant']),
                    new Length([
                        'max'=>180,
                        'maxMessage'=>'L\'adresse Email ne peut contenir plus de 180 caractère'
                    ]),
                    new Email(['message'=>'Cette adresse n\'est pas une adresse Email valide']),
                ]
            ])
            ->add('pseudo',TextType::class,[
                'constraints'=>[
                    new NotBlank(['message'=>'Pseudo manquant']),
                    new Length([
                        'max'=>30,
                        'maxMessage'=>'Le pseudo ne peut contenir plus de 180 caractère',
                        'min'=>3,
                        'minMessage'=>'Le pseudo ne peut contenir au moins de 3 caractère',
                        
                    ]),
                    new Regex([
                        'pattern'=>'/^[A-Za-z0-9_-]+$/',
                        'message'=>'Le pseudo ne peut contenir que des chiffres, lettres tirets et underscores'
                    ])
                ]
            ])
       
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => User::class,
        ]);
    }
}
