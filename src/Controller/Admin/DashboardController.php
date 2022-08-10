<?php
declare(strict_types=1);
namespace App\Controller\Admin;

use App\Entity\League;
use App\Entity\LeagueConfig;
use App\Entity\Place;
use App\Entity\Plan;
use App\Entity\Players;
use App\Entity\Team;
use App\Entity\Tenancy;
use Doctrine\ORM\EntityManagerInterface;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Config\Dashboard;
use EasyCorp\Bundle\EasyAdminBundle\Config\MenuItem;
use EasyCorp\Bundle\EasyAdminBundle\Config\UserMenu;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractDashboardController;
use EasyCorp\Bundle\EasyAdminBundle\Security\Permission;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Attribute\AsController;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\User\UserInterface;

#[AsController]
class DashboardController extends AbstractDashboardController
{
    public function __construct(private EntityManagerInterface $entityManager, private RequestStack $requestStack){}


    #[Route('/admin_c')]
    public function index(): Response
    {
        if(false === $this->isGranted('ROLE_ADMIN')) {
            return $this->redirectToRoute('app.index');
        }

        return $this->render('admin/dashboard.html.twig');
    }

    public function configureDashboard(): Dashboard
    {
        $tenantId = $this->requestStack->getSession()->get('tenancy-site-id', null);
        $tenant = $this->entityManager->getRepository(Tenancy::class)->find($tenantId);
        return Dashboard::new()
            ->setTitle($tenant->getSiteName());
    }

    public function configureMenuItems(): iterable
    {
        yield MenuItem::linkToDashboard('Dashboard', 'fa fa-home');
        yield MenuItem::linkToCrud('Liga-Konfiguration', 'fas fa-list', LeagueConfig::class);
        yield MenuItem::linkToCrud('Ligen', 'fas fa-list', League::class);
        yield MenuItem::linkToCrud('Lokale', 'fas fa-list', Place::class);
        yield MenuItem::linkToCrud('Teams', 'fas fa-list', Team::class);
        yield MenuItem::linkToCrud('Spieler', 'fas fa-list', Players::class);
        yield MenuItem::linkToCrud('Spielpläne', 'fas fa-list', Plan::class);
    }

    public function configureUserMenu(UserInterface $user): UserMenu
    {
        $menu = parent::configureUserMenu($user);
        $menu->setMenuItems([]);
        $newMenu = [MenuItem::linkToLogout('Logout', 'fa fa-sign-out')];

        if ($this->isGranted(Permission::EA_EXIT_IMPERSONATION)) {
            $url = $this->generateUrl('app_admin_superdashboard_index',['_switch_user' => '_exit']);
            $newMenu[] = MenuItem::linkToUrl('Exit Impersonation', 'fa-user-lock', $url);
        }

        return $menu->addMenuItems($newMenu);
    }
}
