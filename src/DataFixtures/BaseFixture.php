<?php

namespace App\DataFixtures;

use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Faker\Factory;
use Faker\Generator;

/**
 * Classe modèle pour les fixtures
 * On ne peut pas instancier une abstraction
 * 
 */
abstract class BaseFixture extends Fixture
{
    /** @var ObjectManager  */
    private $manager;
    /** @var Generator  */
    protected $faker;

    /** @var array liste des références connues cf memoisation*/
    private $references = [];


    /**
     * Méthode à implémenter par les classes qui héritent pour generer les fausses données
     * 
     */
    abstract protected function loadData();

    /**
     * Méthode appelée par le système de fixtures
     */
    public function load(ObjectManager $manager)
    {
        // On enregistre le ObjectManager
        $this->manager = $manager;
        // On instancie Faker
        $this->faker = Factory::create('fr_FR');

        // On appelle loadData() pour avoir les fausses données
        $this->loadData();
        // On exécute l'enregistrement en base
        $this->manager->flush();
    }

    /**
     * Enregistré plusieurs entités
     * @param int $count nombre d'entités
     * @param string $groupName nom du groupe de références
     * @param callable $factory fonction qui génére l'entité
     */

     protected function createMany(int $count,string $groupName, callable $factory){
        for($i=0;$i<$count;$i++){
            // on execute $factory qui doit retourner l'entité généré
            $entity =  $factory($i);      
            // Vérifier que l'entité soit retournée 
            if($entity==null){
                throw new \LogicException('L\entité doit être retrounée');
            }

            // On prépare à l'enregistrement de l'entité
            $this->manager->persist($entity);
            
            // Enregistre une référence à l'entité
            $reference = sprintf('%s_%d',$groupName,$i);
            $this->addReference($reference,$entity);

        }
        // $this->manager->flush();
     }

/**
 * Récuperer l'entite par son nom de group 
 * @param string $groupName nom de groupe de références
 */

     protected function getRandomReference(string $groupName)
     {
         // Vérifier si on a déja enregistré les références du groupe demandé
         if(!isset($this->references[$groupName])){
             // Si non, on va rechercher les références 
             $this->references[$groupName] = [];

             // on parcourt la liste des références toutes classe confondues
             foreach($this->referenceRepository->getReferences() as $key =>$ref){
                 // la clé $key correspond à nos references
                 // Si $key commence par $groupName demandé, on le sauvegarde
                if(strpos($key,$groupName)===0){
                    $this->references[$groupName][]=$key;
                }
             }
         }
            //Vérifier que l'on a bien récuperer des references
        if($this->references[$groupName] === [] ){
             throw new \Exception(sprintf('Aucune référence trouvé pour le groupe %s',$groupName));
        }

        // Retourner une entité correspondant à une référence aléatoire

        $randomReference = $this->faker->randomElement($this->references[$groupName]);
        return $this->getReference($randomReference);

     }



}