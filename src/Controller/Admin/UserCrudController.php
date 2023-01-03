<?php
declare(strict_types=1);
namespace App\Controller\Admin;

use App\Admin\Filter\EnumRoleFilter;
use App\Entity\User;
use Doctrine\ORM\QueryBuilder;
use EasyCorp\Bundle\EasyAdminBundle\Collection\FieldCollection;
use EasyCorp\Bundle\EasyAdminBundle\Collection\FilterCollection;
use EasyCorp\Bundle\EasyAdminBundle\Config\Action;
use EasyCorp\Bundle\EasyAdminBundle\Config\Actions;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Config\Filters;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Dto\EntityDto;
use EasyCorp\Bundle\EasyAdminBundle\Dto\SearchDto;
use EasyCorp\Bundle\EasyAdminBundle\Field\ArrayField;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Field\ChoiceField;
use EasyCorp\Bundle\EasyAdminBundle\Field\EmailField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpKernel\Attribute\AsController;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

#[IsGranted('ROLE_ADMIN'), AsController]
class UserCrudController extends AbstractCrudController
{
    public function __construct(private readonly UrlGeneratorInterface $urlGenerator, private readonly RequestStack $requestStack){}

    public static function getEntityFqcn(): string
    {
        return User::class;
    }


    public function configureFields(string $pageName): iterable
    {

        $id = IdField::new('id')->hideOnForm();
        $email = EmailField::new('email');
        $first = TextField::new('firstname');
        $last = TextField::new('lastname');
        $role = ChoiceField::new('roles')
            ->setChoices(array_combine(['Superadmin','Admin', 'User'], User::USER_ROLES))
            ->allowMultipleChoices()
            ->renderExpanded()
            ->renderAsBadges();
        $pass = TextField::new('password')->setFormType(PasswordType::class);
        #$team = AssociationField::new('team');
        $tenant = AssociationField::new('tenancy')->setLabel('Saas');


        if (Crud::PAGE_INDEX === $pageName) {
            return [$id, $first, $last, $email, $role, $tenant];
        }

        if(Crud::PAGE_EDIT === $pageName) {
            return [
                $id,
                $first,
                $last,
                $email,
                $tenant,
                $role,
            ];
        }

        return [$id, $first, $last, $email, $role, $pass, $tenant];
    }

    public function createIndexQueryBuilder(SearchDto $searchDto, EntityDto $entityDto, FieldCollection $fields, FilterCollection $filters): QueryBuilder
    {
        $qb = parent::createIndexQueryBuilder($searchDto, $entityDto, $fields, $filters);
        $session = $this->requestStack->getSession();
        $tenancyId = $session->get('tenancy-site-id');

        // only enclose the items when Role is not Superadmin and SaaS-ID is present
        if (null !== $tenancyId && !$this->isGranted('ROLE_SUPERADMIN')) {
            $qb->join('entity.tenancy', 't')
                ->andWhere('t.id = :id')
                ->setParameter('id', $tenancyId)
            ;
        }

        return $qb;
    }

    public function configureCrud(Crud $crud): Crud
    {
        return $crud;
    }

    public function configureActions(Actions $actions): Actions
    {

        $impersonate = Action::new('impersonate', 'Als Benutzer anmelden')
            ->linkToUrl(function (User $user): string {
                return $this->urlGenerator->generate('app_admin_dashboard_index', [
                    '_switch_user' => $user->getEmail(),
                ],
                    UrlGeneratorInterface::ABSOLUTE_URL
                );
            });

        return $actions
            ->disable('show', 'delete')
            ->add(Crud::PAGE_INDEX, $impersonate)
            ;
    }

    public function configureFilters(Filters $filters): Filters
    {
        return $filters
            ->add('firstname')
            ->add('lastname')
            ->add('email')
            ->add(EnumRoleFilter::new('roles'))
            ->add('tenancy')
            ;
    }

}
