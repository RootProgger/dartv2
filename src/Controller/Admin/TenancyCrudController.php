<?php
declare(strict_types=1);

namespace App\Controller\Admin;

use App\Entity\Tenancy;
use EasyCorp\Bundle\EasyAdminBundle\Config\Action;
use EasyCorp\Bundle\EasyAdminBundle\Config\Actions;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Config\Filters;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\BooleanField;
use EasyCorp\Bundle\EasyAdminBundle\Field\HiddenField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use EasyCorp\Bundle\EasyAdminBundle\Filter\BooleanFilter;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Component\HttpKernel\Attribute\AsController;

#[IsGranted('ROLE_SUPERADMIN')]
class TenancyCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return Tenancy::class;
    }

    public function configureFields(string $pageName): iterable
    {
        yield IdField::new('id')->hideOnForm();
        yield TextField::new('siteUrl');
        yield TextField::new('siteName');
        yield TextField::new('slug');

        switch ($pageName) {
            case Crud::PAGE_INDEX:
            case Crud::PAGE_DETAIL:
                yield BooleanField::new('default')->renderAsSwitch(false);
                break;
            default:
                yield BooleanField::new('default');
        }
    }
    public function configureActions(Actions $actions): Actions
    {
        return $actions->add(Crud::PAGE_INDEX, Action::DETAIL);
    }

    public function configureFilters(Filters $filters): Filters
    {
        return $filters
            ->add('siteUrl')
            ->add('siteName')
            ->add('slug')
            ->add(BooleanFilter::new('default'))
            ;
    }
}
