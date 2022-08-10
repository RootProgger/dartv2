<?php

namespace App\Navigation;

use App\Entity\Tenancy;
use Doctrine\ORM\EntityManagerInterface;
use Knp\Menu\FactoryInterface;
use Knp\Menu\ItemInterface;
use Symfony\Component\HttpFoundation\RequestStack;

final class MenuBuilder
{

    public function __construct(private FactoryInterface $factory, private RequestStack $requestStack, private EntityManagerInterface $entityManager)
    {
    }

    public function mainMenu(): ItemInterface
    {
        $menu = $this->factory->createItem('root');
        $menu
            ->setChildrenAttributes([
                'class' => 'navbar-nav'
            ])
            ;

        $menu->addChild('Home', [
            'route' => 'app.index'
        ]);

        $menu->addChild('Spielplan', [
            'route' => 'app.plan'
        ]);


        return $menu;
    }
}
