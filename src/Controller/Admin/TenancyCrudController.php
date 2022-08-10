<?php
declare(strict_types=1);

namespace App\Controller\Admin;

use App\Entity\Tenancy;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Component\HttpKernel\Attribute\AsController;

#[IsGranted('ROLE_SUPERADMIN')]
class TenancyCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return Tenancy::class;
    }
}
