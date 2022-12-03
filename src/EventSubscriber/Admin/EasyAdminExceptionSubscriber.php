<?php

namespace App\EventSubscriber\Admin;

use App\Exceptions\PlanFailedException;
use EasyCorp\Bundle\EasyAdminBundle\Provider\AdminContextProvider;
use EasyCorp\Bundle\EasyAdminBundle\Router\AdminUrlGenerator;
use JetBrains\PhpStorm\ArrayShape;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpKernel\Event\ExceptionEvent;
use Symfony\Component\HttpKernel\KernelEvents;

class EasyAdminExceptionSubscriber implements EventSubscriberInterface
{

    public function __construct(private RequestStack $requestStack, private AdminContextProvider $adminContextProvider, private AdminUrlGenerator $adminUrlGenerator){}

    public function onKernelException(ExceptionEvent $exceptionEvent): void
    {
        $exception = $exceptionEvent->getThrowable();
        if(false === $exception instanceof PlanFailedException)
        {
            return;
        }

        // Check if exception happened in EasyAdmin (avoid warning outside EA)
        if(!$this->adminContextProvider || !$this->adminContextProvider->getContext()) return;

        // Get back crud information
        $crud       = $this->adminContextProvider->getContext()->getCrud();
        if(!$crud) return;

        $controller = $crud->getControllerFqcn();
        $action     = $crud->getCurrentPage();


        $url = $this->adminUrlGenerator->unsetAll();
        $url = $url->setController($controller);
        if(null !== $this->requestStack->getCurrentRequest()->get('crudAction'))
        {
            $url->setAction($action);
        }
        $this->requestStack->getSession()->getFlashBag()->add('danger', $exception->getMessage());

        $exceptionEvent->setResponse(new RedirectResponse($url));



    }

    #[ArrayShape([KernelEvents::EXCEPTION => "string"])]
    public static function getSubscribedEvents(): array
    {
        return [
            KernelEvents::EXCEPTION => 'onKernelException',
        ];
    }
}
