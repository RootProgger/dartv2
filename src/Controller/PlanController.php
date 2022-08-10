<?php

namespace App\Controller;

use App\Entity\League;
use App\Entity\Tenancy;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/plan')]
class PlanController extends BaseAbstractController
{
    #[Route('/', name: 'app.plan')]
    public function index(SessionInterface $session, EntityManagerInterface $entityManager): Response
    {
        $tenantId = $session->get('tenancy-site-id');
        $tenant = $entityManager->getRepository(Tenancy::class)->find($tenantId);

        return $this->render('plan/index.html.twig', [
            'leagues' => $tenant->getLeagues(),
        ]);
    }

    public function _planTab(League $league)
    {
        #dd($league->getFirstActivePlan());
        return $this->render('plan/_planTab.html.twig', [
            'league' => $league
        ]);
    }
}
