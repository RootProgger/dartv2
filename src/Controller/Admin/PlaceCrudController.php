<?php
declare(strict_types=1);
namespace App\Controller\Admin;

use App\Entity\Place;
use App\Entity\Tenancy;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\QueryBuilder;
use EasyCorp\Bundle\EasyAdminBundle\Collection\FieldCollection;
use EasyCorp\Bundle\EasyAdminBundle\Collection\FilterCollection;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Dto\EntityDto;
use EasyCorp\Bundle\EasyAdminBundle\Dto\SearchDto;
use EasyCorp\Bundle\EasyAdminBundle\Field\HiddenField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\NumberField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Component\HttpFoundation\RequestStack;

#[IsGranted('ROLE_ADMIN')]
class PlaceCrudController extends AbstractCrudController
{
    private Tenancy $tenancy;

    public function __construct(RequestStack $requestStack, EntityManagerInterface $entityManager)
    {
        $siteId = $requestStack->getSession()->get('tenancy-site-id');
        $this->tenancy = $entityManager->getRepository(Tenancy::class)->find($siteId);
    }

    public static function getEntityFqcn(): string
    {
        return Place::class;
    }

    public function configureCrud(Crud $crud): Crud
    {

        return $crud
            ->setEntityLabelInPlural('Spielstätten')
            ->setEntityLabelInSingular('Spielstätte');
    }

    public function createIndexQueryBuilder(SearchDto $searchDto, EntityDto $entityDto, FieldCollection $fields, FilterCollection $filters): QueryBuilder
    {
        return parent::createIndexQueryBuilder($searchDto, $entityDto, $fields, $filters)
            ->andWhere('entity.tenancy = :ten')
            ->setParameter('ten', $this->tenancy);
    }


    public function configureFields(string $pageName): iterable
    {

        #dd($this->tenancy);
        yield IdField::new('id')->hideOnForm();
        yield TextField::new('name');
        yield TextField::new('street')->setLabel('Straße');
        yield TextField::new('zip')->setLabel('Plz');
        yield TextField::new('city')->setLabel('Stadt');
        yield NumberField::new('devicesCount')->setLabel('Spielautomaten');

    }

}
