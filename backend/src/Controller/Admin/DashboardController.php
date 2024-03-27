<?php

namespace App\Controller\Admin;

use EasyCorp\Bundle\EasyAdminBundle\Config\Dashboard;
use EasyCorp\Bundle\EasyAdminBundle\Config\MenuItem;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractDashboardController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
// use categorie controller
use App\Controller\Admin\CategorieCrudController;

use EasyCorp\Bundle\EasyAdminBundle\Router\AdminUrlGenerator;

use App\Entity\Categorie;
use App\Entity\Utilisateur;
use App\Entity\Ressource;
use App\Entity\TypeRessource;
use App\Entity\Commentaire;


class DashboardController extends AbstractDashboardController
{


    #[Route('/admin', name: 'admin')]
    public function index(): Response
    {
        // return parent::index();

        // Option 1. You can make your dashboard redirect to some common page of your backend
        //
        $adminUrlGenerator = $this->container->get(AdminUrlGenerator::class);
        return $this->redirect($adminUrlGenerator->setController(CategorieCrudController::class)->generateUrl());

        // Option 2. You can make your dashboard redirect to different pages depending on the user
        //
        // if ('jane' === $this->getUser()->getUsername()) {
        //     return $this->redirect('...');
        // }

        // Option 3. You can render some custom template to display a proper dashboard with widgets, etc.
        // (tip: it's easier if your template extends from @EasyAdmin/page/content.html.twig)
        //
        // return $this->render('some/path/my-dashboard.html.twig');
    }

    public function configureDashboard(): Dashboard
    {
        return Dashboard::new()
        // the name visible to end users
        ->setTitle('Ressources Relationnelles.')
        // you can include HTML contents too (e.g. to link to an image)
        ->setTitle('<img src="..."> Ressources Relationnelles</span>')
        ->setFaviconPath('favicon.svg')
        // ->setTranslationDomain('my-custom-domain')
        ->setTextDirection('ltr')
        ->renderContentMaximized()
        ->renderSidebarMinimized()
        // ->disableDarkMode()
        ->generateRelativeUrls()
        ->setLocales(['fr']);
    }
    

    public function configureMenuItems(): iterable
    {
        yield MenuItem::linkToDashboard('Tablau de bord', 'fa fa-home');
        yield MenuItem::linkToCrud('Gérer les utilisateurs', 'fas fa-user', Utilisateur::class);
        yield MenuItem::linkToCrud('Gérer les catégories', 'fas fa-list', Categorie::class);
        yield MenuItem::linkToCrud('Gérer les ressources', 'fas fa-list', Ressource::class);
        yield MenuItem::linkToCrud('Gérer les types de ressources', 'fas fa-list', TypeRessource::class);
        yield MenuItem::linkToCrud('Gérer les commentaires', 'fas fa-question', Commentaire::class);

    }
}
