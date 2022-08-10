<?php
declare(strict_types=1);
namespace App\Controller\Admin;

use App\Entity\Plan;
use App\Entity\Tenancy;
use App\Service\PlanGenerator;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\QueryBuilder;
use EasyCorp\Bundle\EasyAdminBundle\Collection\FieldCollection;
use EasyCorp\Bundle\EasyAdminBundle\Collection\FilterCollection;
use EasyCorp\Bundle\EasyAdminBundle\Config\Action;
use EasyCorp\Bundle\EasyAdminBundle\Config\Actions;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Dto\EntityDto;
use EasyCorp\Bundle\EasyAdminBundle\Dto\SearchDto;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Field\BooleanField;
use EasyCorp\Bundle\EasyAdminBundle\Field\DateField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IntegerField;
use EasyCorp\Bundle\EasyAdminBundle\Field\NumberField;
use EasyCorp\Bundle\EasyAdminBundle\Router\AdminUrlGenerator;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Component\HttpFoundation\RequestStack;

#[IsGranted('ROLE_ADMIN')]
class PlanCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return Plan::class;
    }

    private Tenancy $tenancy;

    public function __construct(private AdminUrlGenerator $urlGenerator, EntityManagerInterface $entityManager, RequestStack $requestStack, private PlanGenerator $planGenerator){
        $tenantId = $requestStack->getSession()->get('tenancy-site-id');
        $this->tenancy = $entityManager->getRepository(Tenancy::class)->find($tenantId);
    }

    public function configureCrud(Crud $crud): Crud
    {
        return $crud
            ->setEntityLabelInSingular('Spielplan')
            ->setEntityLabelInPlural('Spielpläne')
            ;
    }

    public function persistEntity(EntityManagerInterface $entityManager, $entityInstance): void
    {
        try{
            $entityInstance = $this->planGenerator->generate($entityInstance);

            $entityManager->persist($entityInstance);
            $entityManager->flush();
        } catch (\Exception $e)
        {
            $this->addFlash('error', $e->getMessage());
        }
    }

    /**
     * @param EntityManagerInterface $entityManager
     * @param Plan $entityInstance
     * @return void
     */
    public function updateEntity(EntityManagerInterface $entityManager, $entityInstance): void
    {
        if(null !== $entityInstance->getPauseStart()) {
            try{
                $this->planGenerator->addPause($entityInstance);
            } catch (\InvalidArgumentException $e) {
                $this->addFlash('error',$e->getMessage());
            }
        }


    }

    public function createIndexQueryBuilder(SearchDto $searchDto, EntityDto $entityDto, FieldCollection $fields, FilterCollection $filters): QueryBuilder
    {
        return parent::createIndexQueryBuilder($searchDto, $entityDto, $fields, $filters)
            ->join('entity.league', 'l')
            ->andWhere('l.tenancy = :ten')
            ->setParameter('ten', $this->tenancy)
            ;
    }

    public function configureActions(Actions $actions): Actions
    {
        $urlObj = $this->urlGenerator
            ->setController(PlanRowCrudController::class)
            ->setAction(Action::INDEX)
            ->setDashboard(DashboardController::class)
            ;

        $viewPlan = Action::new('showRows', 'Detail', 'fa fa-envelope')
            ->linkToUrl(fn (Plan $plan) => $urlObj->set('plan', $plan->getId())->generateUrl());
        return $actions
            ->add(Crud::PAGE_INDEX, $viewPlan);
    }

    public function configureFields(string $pageName): iterable
    {
        yield IdField::new('id')->hideOnForm();
        yield AssociationField::new('league')->setLabel('Liga');
        yield BooleanField::new('active')->setLabel('Aktiv');
        yield DateField::new('startDate')->setLabel('Start Datum')->setHelp('Startdatum muss zwingend ein Montag sein, sonst funktioniert die Kalkulation der Runden nicht');
        yield DateField::new('pauseStart')->hideOnIndex()->hideOnDetail()->setLabel('Pause Start')->setHelp('Sie können weitere Pausen einfügen wenn Sie diesen Plan editieren. Start muss ein Montag sein');
        yield IntegerField::new('pauseLength')->hideOnIndex()->hideOnDetail()->setLabel('Pause Länge (Wochen)')->setHelp('Sie können weitere Pausen einfügen wenn Sie diesen Plan editieren. Die länge bitte in Wochen angeben');
    }

}
