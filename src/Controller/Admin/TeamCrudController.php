<?php
declare(strict_types=1);
namespace App\Controller\Admin;

use App\Admin\Fields\EnumField;
use App\Entity\Team;
use App\Entity\Tenancy;
use App\Enum\GameDay;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\QueryBuilder;
use EasyCorp\Bundle\EasyAdminBundle\Collection\FieldCollection;
use EasyCorp\Bundle\EasyAdminBundle\Collection\FilterCollection;
use EasyCorp\Bundle\EasyAdminBundle\Config\Action;
use EasyCorp\Bundle\EasyAdminBundle\Config\Actions;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Dto\EntityDto;
use EasyCorp\Bundle\EasyAdminBundle\Dto\SearchDto;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Field\ChoiceField;
use EasyCorp\Bundle\EasyAdminBundle\Field\HiddenField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Component\HttpFoundation\RequestStack;

#[IsGranted('ROLE_ADMIN')]
class TeamCrudController extends AbstractCrudController
{
    private Tenancy $tenancy;

    public function __construct(RequestStack $requestStack, EntityManagerInterface $entityManager){
        $siteId = $requestStack->getSession()->get('tenancy-site-id');
        $this->tenancy = $entityManager->getRepository(Tenancy::class)->find($siteId);
    }

    public static function getEntityFqcn(): string
    {
        return Team::class;
    }


    public function configureFields(string $pageName): iterable
    {

            yield IdField::new('id')->hideOnForm();
            yield TextField::new('name');
            yield AssociationField::new('league')->setLabel('Liga');
            yield AssociationField::new('place')->setLabel('Spielstätte');
            //yield HiddenField::new('tenancy');
               yield AssociationField::new('tenancy')->setLabel('SaaS')->hideOnDetail()->setPermission('IS_IMPERSONATOR')->setQueryBuilder(
                   fn(QueryBuilder $queryBuilder) => $queryBuilder->andWhere('entity.siteName != :name')->setParameter('name', 'Administration')
               );
            yield EnumField::new('day')->setFormTypeOptions([
                'class' => GameDay::class
            ])->setLabel('Spieltag')->formatValue(fn ($value) => ($value instanceof \BackedEnum) ? $value->name : null)->setHelp('Wird von Liga falls gesetzt überschrieben.');

    }


    public function createIndexQueryBuilder(SearchDto $searchDto, EntityDto $entityDto, FieldCollection $fields, FilterCollection $filters): QueryBuilder
    {
        return parent::createIndexQueryBuilder($searchDto, $entityDto, $fields, $filters)
            ->andWhere('entity.tenancy = :tenant')
            ->setParameter('tenant', $this->tenancy);
    }

}
