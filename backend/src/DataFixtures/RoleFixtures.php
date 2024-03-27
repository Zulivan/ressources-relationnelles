<?php
namespace App\DataFixtures;

use App\Entity\Role;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class RoleFixtures extends Fixture
{
    public function load(ObjectManager $manager)
    {
        $libelle = "Super-Administrateur";
        $role = new Role();
        $role->setLibelle($libelle);
        $manager->persist($role);
        $manager->flush();
        $this->setReference('role_1', $role);


        $libelle = "Administrateur";
        $role = new Role();
        $role->setLibelle($libelle);
        $manager->persist($role);
        $manager->flush();
        $this->setReference('role_2', $role);


        $libelle = "Modérateur";
        $role = new Role();
        $role->setLibelle($libelle);
        $manager->persist($role);
        $manager->flush();
        $this->setReference('role_3', $role);


        $libelle = "Citoyen";
        $role = new Role();
        $role->setLibelle($libelle);
        $manager->persist($role);
        $manager->flush();
        $this->setReference('role_4', $role);

    }
}