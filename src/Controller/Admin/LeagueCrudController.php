<?php
declare(strict_types=1);
namespace App\Controller\Admin;

use App\Admin\Fields\EnumField;
use App\Entity\League;
use App\Entity\Tenancy;
use App\Enum\GameDay;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\QueryBuilder;
use EasyCorp\Bundle\EasyAdminBundle\Collection\FieldCollection;
use EasyCorp\Bundle\EasyAdminBundle\Collection\FilterCollection;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Dto\EntityDto;
use EasyCorp\Bundle\EasyAdminBundle\Dto\SearchDto;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Component\HttpFoundation\RequestStack;

#[IsGranted('ROLE_ADMIN')]
class LeagueCrudController extends AbstractCrudController
{
    private Tenancy $tenancy;

    public function __construct(EntityManagerInterface $entityManager, RequestStack $requestStack){
        $tenantId = $requestStack->getSession()->get('tenancy-site-id');
        $this->tenancy = $entityManager->getRepository(Tenancy::class)->find($tenantId);
    }

    public static function getEntityFqcn(): string
    {
        return League::class;
    }

    public function createIndexQueryBuilder(SearchDto $searchDto, EntityDto $entityDto, FieldCollection $fields, FilterCollection $filters): QueryBuilder
    {
        return parent::createIndexQueryBuilder($searchDto, $entityDto, $fields, $filters)
            ->andWhere('entity.tenancy = :ten')
            ->setParameter('ten', $this->tenancy);
    }

    public function configureCrud(Crud $crud): Crud
    {
        return $crud
            ->setEntityLabelInPlural('Ligen')
            ->setEntityLabelInSingular('Liga')
            ;
    }

    public function configureFields(string $pageName): iterable
    {

          yield IdField::new('id')->hideOnForm();
          yield TextField::new('name');
          yield AssociationField::new('leagueConfig')->setLabel('Liga Konfiguration');
          yield EnumField::new('day')->setFormTypeOptions([
              'class' => GameDay::class
          ])->setLabel('Spieltag')->formatValue(fn ($value) => ($value instanceof \BackedEnum) ? $value->name : null)->setHelp('Wenn sie hier den Spieltag setzen, tritt der Spieltag im Team ausser Kraft');
    }

}
