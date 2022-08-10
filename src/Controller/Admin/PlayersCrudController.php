<?php
declare(strict_types=1);
namespace App\Controller\Admin;

use App\Entity\Players;
use App\Entity\Team;
use App\Entity\Tenancy;
use Doctrine\Common\Collections\Criteria;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\QueryBuilder;
use EasyCorp\Bundle\EasyAdminBundle\Collection\FieldCollection;
use EasyCorp\Bundle\EasyAdminBundle\Collection\FilterCollection;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Dto\EntityDto;
use EasyCorp\Bundle\EasyAdminBundle\Dto\SearchDto;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use SebastianBergmann\CodeCoverage\Report\Text;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Component\HttpFoundation\RequestStack;
#[IsGranted('ROLE_ADMIN')]
class PlayersCrudController extends AbstractCrudController
{
    private Tenancy $tenancy;

    public function __construct(EntityManagerInterface $entityManager, RequestStack $requestStack){
        $tenantId = $requestStack->getSession()->get('tenancy-site-id');
        $this->tenancy = $entityManager->getRepository(Tenancy::class)->find($tenantId);
    }

    public static function getEntityFqcn(): string
    {
        return Players::class;
    }

    public function configureFields(string $pageName): iterable
    {

        $criteria = Criteria::create();
        $criteria->where(Criteria::expr()->eq('tenancy', $this->tenancy))->orderBy(['name' => 'ASC']);

            yield IdField::new('id')->hideOnForm();
            yield TextField::new('firstname');
            yield TextField::new('lastname');
            yield AssociationField::new('team')->setQueryBuilder(
                fn(QueryBuilder $queryBuilder) => $queryBuilder->addCriteria($criteria)
            );
    }

    public function createIndexQueryBuilder(SearchDto $searchDto, EntityDto $entityDto, FieldCollection $fields, FilterCollection $filters): QueryBuilder
    {
        return parent::createIndexQueryBuilder($searchDto, $entityDto, $fields, $filters)
            ->andWhere('entity.tenancy = :ten')
            ->setParameter('ten', $this->tenancy);
    }

}
