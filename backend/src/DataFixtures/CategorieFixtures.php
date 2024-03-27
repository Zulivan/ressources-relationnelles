<?php
namespace App\DataFixtures;

use App\Entity\Categorie;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class CategorieFixtures extends Fixture
{
    public function load(ObjectManager $manager)
    {
        $libelle = "Santé";
        $categorie = new Categorie();
        $categorie->setLibelle($libelle);
        $manager->persist($categorie);
        $manager->flush();
      
        $libelle = "Conseil";
        $categorie = new Categorie();
        $categorie->setLibelle($libelle);
        $manager->persist($categorie);
        $manager->flush();
      
        $libelle = "Information";
        $categorie = new Categorie();
        $categorie->setLibelle($libelle);
        $manager->persist($categorie);
        $manager->flush();
      
    }
}