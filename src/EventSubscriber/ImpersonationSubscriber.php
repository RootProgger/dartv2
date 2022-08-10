<?php
declare(strict_types=1);

namespace App\EventSubscriber;

use App\Entity\User;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Security\Http\Event\SwitchUserEvent;
use Symfony\Component\Security\Http\SecurityEvents;

class ImpersonationSubscriber implements EventSubscriberInterface
{

    public function onSwitchUser(SwitchUserEvent $event): void
    {
        $session = $event->getRequest()->getSession();
        /** @var User $userSwitchedTo */
        $userSwitchedTo = $event->getToken()->getUser();
        $tenancy = $userSwitchedTo->getTenancy();
        $session->set('tenancy-site-id', $tenancy->getId());
    }

    public static function getSubscribedEvents(): array
    {
        return [
            SecurityEvents::SWITCH_USER => 'onSwitchUser',
        ];
    }
}
