<?php

namespace App\DataFixtures;

use App\Entity\Event;
use App\Entity\User;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

;
class EventFixtures extends BaseFixture
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
        $this->createMany($this->faker->numberBetween(1,10),'event',function(int $num){
         return   (new Event())
            ->setAuthor($this->getRandomReference('user_admin'))
            ->setName($this->faker->lastName)
            ->setDescription($this->faker->optional()->realText(250))
            ->setEventDate($this->faker->dateTimeBetween('-2 years'))
            // ->addParticipant($this->getRandomReference('user_admin'))
         
            ;

        });

    }
    public function getDependencies()
    {
        return [
            AUserFixtures::class,
            EventFixtures::class
        ];
    }
}