<?php
declare(strict_types=1);
namespace App\EventSubscriber;

use App\Entity\Tenancy;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpKernel\Event\RequestEvent;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\HttpKernel\KernelEvents;

class KernelRequestSubscriber implements EventSubscriberInterface
{
    public function __construct(private EntityManagerInterface $entityManager){}

    public function onKernelRequest(RequestEvent $event): void
    {
        $session = $event->getRequest()->getSession();
        $tenancy = $session->get('tenancy-site-id', null);
        if (null === $tenancy) {
            $tenancyEntity = $this->entityManager->getRepository(Tenancy::class)->findOneBy([
                'siteUrl' => $_SERVER['HTTP_HOST'],
            ]);
            if(null === $tenancyEntity) {
                throw new NotFoundHttpException(sprintf('no tenant with site-Url: "%s" found!', $_SERVER['HTTP_HOST']));
            }
            $session->set('tenancy-site-id', $tenancyEntity->getId());
        }
    }

    public static function getSubscribedEvents(): array
    {
        return [
            KernelEvents::REQUEST => 'onKernelRequest',
        ];
    }
}
