<?php
declare(strict_types=1);
namespace App\EventSubscriber;

use App\Entity\League;
use App\Entity\LeagueConfig;
use App\Entity\Place;
use App\Entity\Plan;
use App\Entity\Players;
use App\Entity\Team;
use App\Entity\Tenancy;
use App\Entity\User;
use App\Service\PlanGenerator;
use Doctrine\ORM\EntityManagerInterface;
use EasyCorp\Bundle\EasyAdminBundle\Event\AfterEntitySearchEvent;
use EasyCorp\Bundle\EasyAdminBundle\Event\BeforeCrudActionEvent;
use EasyCorp\Bundle\EasyAdminBundle\Event\BeforeEntityPersistedEvent;
use EasyCorp\Bundle\EasyAdminBundle\Event\BeforeEntityUpdatedEvent;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class EasyAdminSubscriber implements EventSubscriberInterface
{
    private Tenancy $tenancy;

    public function __construct(private UserPasswordHasherInterface $passwordHasher, RequestStack $requestStack, EntityManagerInterface $entityManager){
        $tenantId = $requestStack->getSession()->get('tenancy-site-id');
        $this->tenancy = $entityManager->getRepository(Tenancy::class)->find($tenantId);
    }

    public function onBeforeEntityPersistEvent(BeforeEntityPersistedEvent $event): void
    {
        $entity = $event->getEntityInstance();
        if(($entity instanceof User)) {
            $password = $entity->getPassword();
            $entity->setPassword($this->passwordHasher->hashPassword($entity, $password));
        }


        // inject Tenancy automatically instead do wired stuff in crud-controller
        if(
            $entity instanceof LeagueConfig ||
            $entity instanceof Team ||
            $entity instanceof League ||
            $entity instanceof Place ||
            $entity instanceof Players
        )
        {
            if(null === $entity->getTenancy())
            {
                $entity->setTenancy($this->tenancy);
            }
        }


    }

    public function onBeforeEntityUpdatedEvent(BeforeEntityUpdatedEvent $event): void
    {
        $entity = $event->getEntityInstance();
        if(method_exists($entity, 'getTenancy') && null === $entity->getTenancy())
        {
            $entity->setTenancy($this->tenancy);
        }
    }

    public function onBeforeCrudActionEvent(BeforeCrudActionEvent $event): void
    {
        #dd($event);
    }


    public static function getSubscribedEvents(): array
    {
        return [
            BeforeEntityPersistedEvent::class => 'onBeforeEntityPersistEvent',
            BeforeEntityUpdatedEvent::class => 'onBeforeEntityUpdatedEvent',
            BeforeCrudActionEvent::class => 'onBeforeCrudActionEvent',
            #AfterEntitySearchEvent::class => 'onAfterEntitySearchEvent',
        ];
    }
}
