<?php
declare(strict_types=1);
namespace App\Controller\Admin;

use App\Entity\Team;
use App\Entity\Tenancy;
use App\Entity\User;
use EasyCorp\Bundle\EasyAdminBundle\Config\Dashboard;
use EasyCorp\Bundle\EasyAdminBundle\Config\MenuItem;
use EasyCorp\Bundle\EasyAdminBundle\Context\AdminContext;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractDashboardController;
use EasyCorp\Bundle\EasyAdminBundle\Provider\AdminContextProvider;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Attribute\AsController;
use Symfony\Component\Routing\Annotation\Route;

#[AsController, IsGranted('IS_AUTHENTICATED_FULLY')]
class SuperDashboardController extends AbstractDashboardController
{
    #public function __construct(){}

    #[Route('/admin_s')]
    public function index(): Response
    {
        if(false === $this->isGranted('ROLE_SUPERADMIN')){

            return $this->redirectToRoute('/admin_c');
        }
        return $this->render('admin/dashboard.html.twig');
    }

    public function configureDashboard(): Dashboard
    {
        return Dashboard::new()
            ->setTitle('Admin Area');
    }

    public function configureMenuItems(): iterable
    {
        yield MenuItem::linkToDashboard('Dashboard', 'fa fa-home');
        yield MenuItem::linkToCrud('User', 'fas fa-list', User::class);
        yield MenuItem::linkToCrud('SaaS', 'fas fa-list', Tenancy::class);
    }

}
