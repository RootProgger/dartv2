<?php
declare(strict_types=1);

namespace App\Controller\Admin;

use App\Entity\Plan;
use App\Entity\PlanRow;
use App\Entity\Team;
use App\Entity\Tenancy;
use Doctrine\Common\Collections\Criteria;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\QueryBuilder;
use EasyCorp\Bundle\EasyAdminBundle\Collection\FieldCollection;
use EasyCorp\Bundle\EasyAdminBundle\Collection\FilterCollection;
use EasyCorp\Bundle\EasyAdminBundle\Config\Actions;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Dto\EntityDto;
use EasyCorp\Bundle\EasyAdminBundle\Dto\SearchDto;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Field\DateField;
use EasyCorp\Bundle\EasyAdminBundle\Field\NumberField;
use Symfony\Component\HttpFoundation\RequestStack;

class PlanRowCrudController extends AbstractCrudController
{
    private Tenancy $tenancy;

    public function __construct(private RequestStack $requestStack, private EntityManagerInterface $entityManager) {
        $tenantId = $requestStack->getSession()->get('tenancy-site-id');
        $this->tenancy = $entityManager->getRepository(Tenancy::class)->find($tenantId);
    }

    public function configureCrud(Crud $crud): Crud
    {
        return $crud
            ->setEntityLabelInPlural('Spielplan Details')
            ->setEntityLabelInSingular('Spielplan Zeile')
            ;
    }

    public function configureActions(Actions $actions): Actions
    {
        $actions
            ->remove(Crud::PAGE_INDEX, 'new')
            ->remove(Crud::PAGE_INDEX, 'delete')
            ;

        return $actions;
    }

    public static function getEntityFqcn(): string
    {
        return PlanRow::class;
    }

    public function createIndexQueryBuilder(SearchDto $searchDto, EntityDto $entityDto, FieldCollection $fields, FilterCollection $filters): QueryBuilder
    {
        $plan = $this->entityManager->getRepository(Plan::class)->find($this->requestStack->getCurrentRequest()->get('plan'));
        return parent::createIndexQueryBuilder($searchDto, $entityDto, $fields, $filters)
            ->andWhere('entity.plan = :plan')
            ->setParameter('plan', $plan)
            ->orderBy('entity.date', 'ASC')
            ;
    }


    public function configureFields(string $pageName): iterable
    {
        $criteria = Criteria::create();
        $criteria->where(Criteria::expr()->eq('tenancy', $this->tenancy));

        yield NumberField::new('gameDay')->setLabel('Spieltag')->setFormTypeOption('attr', ['readonly' => true]);
        yield AssociationField::new('homeTeam')->setLabel('Heim-Team')->setQueryBuilder(
            fn(QueryBuilder $builder) => $builder->addCriteria($criteria)
        );
        yield AssociationField::new('guestTeam')->setLabel('Gast-Team')->setQueryBuilder(
            fn(QueryBuilder $builder) => $builder->addCriteria($criteria)
        );
        yield DateField::new('date')->setLabel('Datum');
        yield NumberField::new('pointsHome')->setLabel('Punkte Heim')->formatValue(fn($value) => null === $value ? 0 : $value);
        yield NumberField::new('pointsGuest')->setLabel('Punkte Gast')->formatValue(fn($value) => null === $value ? 0 : $value);
        yield NumberField::new('homeSumGames')->setLabel('Spiele Heim')->formatValue(fn($value) => null === $value ? 0 : $value)->hideOnForm();
        yield NumberField::new('guestSumGames')->setLabel('Spiele Gast')->formatValue(fn($value) => null === $value ? 0 : $value)->hideOnForm();
    }

}
