<?php
declare(strict_types=1);
namespace App\Controller\Admin;

use App\Entity\LeagueConfig;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\BooleanField;
use EasyCorp\Bundle\EasyAdminBundle\Field\CollectionField;
use EasyCorp\Bundle\EasyAdminBundle\Field\FormField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IntegerField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;

#[IsGranted('ROLE_ADMIN')]
class LeagueConfigCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return LeagueConfig::class;
    }

    public function configureCrud(Crud $crud): Crud
    {
        return $crud
            ->setEntityLabelInSingular('Konfiguration')
            ->setEntityLabelInPlural('Ligen Konfigurationen')
            ;
    }


    public function configureFields(string $pageName): iterable
    {

           yield IdField::new('id')->hideOnForm();
           yield TextField::new('name');
           yield BooleanField::new('teamgame');
           yield IntegerField::new('pointsWin')->setLabel('Punkte gewonnen');
           yield IntegerField::new('pointsLost')->setLabel('Punkte verloren');
           yield IntegerField::new('pointsTie')->setLabel('Punkte unentschieden');
           yield IntegerField::new('singleGames')->setLabel('Anzahl Einzelspiele');
           yield IntegerField::new('doubleGames')->setLabel('Anzahl Doppelspiele');
           yield CollectionField::new('extraFields')->setLabel('Extra Felder')->setHelp('Hier können Sie zusätzliche Felder anlegen, welche in den Statistiken und beim Spiel eintragen angezeigt werden. Diese Felder werden pro User angezeigt.');

    }

}
