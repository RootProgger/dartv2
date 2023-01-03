<?php

declare(strict_types=1);

namespace App\Controller;

use App\Entity\Tenancy;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Attribute\AsController;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/'), AsController]
class IndexController extends AbstractController
{
    private Tenancy $tenancy;

    public function __construct(
        private readonly RequestStack $requestStack,
        private readonly EntityManagerInterface $entityManager
    ) {
        $tenantId = $this->requestStack->getSession()->get('tenancy-site-id', null);
        $this->tenancy = $this->entityManager->getRepository(Tenancy::class)->find($tenantId);
    }

    #[Route('/', name: 'app.index')]
    public function index(): Response
    {
        if ($this->tenancy->isDefault()) {
            return $this->redirectToRoute('app.login');
        }


        return $this->render('index/index.html.twig', [
            'tenancy' => $this->tenancy,

        ]);
    }
}
