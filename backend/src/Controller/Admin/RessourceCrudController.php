<?php

namespace App\Controller\Admin;

use App\Entity\Ressource;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextareaField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextEditorField;
use EasyCorp\Bundle\EasyAdminBundle\Field\BooleanField;
use EasyCorp\Bundle\EasyAdminBundle\Field\DateTimeField;

class RessourceCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return Ressource::class;
    }

    public function configureFields(string $pageName): iterable
    {
        return [
            TextField::new('titre'),
            TextareaField::new('texte'),
            BooleanField::new('validee'),

            AssociationField::new('createur')
            ->setFormTypeOptions([
                'by_reference' => true,
            ])->autocomplete(),

            AssociationField::new('validateur')
            ->setFormTypeOptions([
                'by_reference' => true,
            ])->autocomplete(),

            AssociationField::new('categories')
            ->setFormTypeOptions([
                'by_reference' => true,
            ])->autocomplete(),

            AssociationField::new('typeRessource')
            ->setFormTypeOptions([
                'by_reference' => true,
            ])->autocomplete()
        ];
    }
}
