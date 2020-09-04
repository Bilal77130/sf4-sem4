<?php

namespace App\DataFixtures;

use App\Entity\User;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

;
class UserFixtures extends BaseFixture
{
    private $encoder;
    /**
     * Dans une classe autre qu'un controller 
     * on peut recuperer des services par 
     * autowiring uniquement dans le constructeur
     */
    public function __construct(UserPasswordEncoderInterface $encoder)
    {
        $this->encoder =$encoder;
    }
    protected function loadData()
    {
        //Administrateurs 
        $this->createMany($this->faker->randomDigit(),'user_admin',function(int $num){
        $admin = new User();
        $password = $this->encoder->encodePassword($admin,'admin'.$num);
        return $admin
                    ->setEmail('admin'.$num.'@kritik.fr')
                    ->setRoles(['ROLE_ADMIN'])
                    ->setPassword($password)
                    ->setPseudo($this->faker->unique()->userName)
                    ->confirmAccount()
                    ->addParticipation($this->getRandomReference('event'))
                    ->renewToken()
                    ;
        });

        // Utilisateurs
        $this->createMany(5,'user_event',function(int $num){
            $user = new User();
            $password = $this->encoder->encodePassword($user,'user'.$num);
            return $user
                        ->setEmail('user'.$num.'@kritik.fr')
                        ->setRoles([])
                        ->setPassword($password)
                        ->setPseudo($this->faker->unique()->userName)
                        ->confirmAccount()
                        ->renewToken()
                        ;
            });

          
    }
}