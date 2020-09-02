<?php

namespace App\DataFixtures;

use App\Entity\Participation;
use App\Entity\User;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

;
class ParticipationsFixtures extends BaseFixture
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
        $this->createMany(5,'participation',function(int $num){
       

       return (new Participation())
            ->setUser($this->getRandomReference('user'))
            ->setEvent($this->getRandomReference('event'))
         
            ;
      

    });  

    }
    public function getDependencies()
    {
        return [
            UserFixtures::class,
            EventFixtures::class
        ];
    }
}