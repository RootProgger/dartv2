<?php

namespace App\Controller;

use App\Dto\EntryDto;
use App\Dto\EntryStep1Dto;
use App\Entity\League;
use App\Entity\PlanRow;
use App\Entity\Tenancy;
use App\Form\EntryStep1Type;
use App\Form\EntryStep2Type;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
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

    public function _planTab(League $league): Response
    {
        return $this->render('plan/_planTab.html.twig', [
            'league' => $league
        ]);
    }

    #[Route('/detail/{row}', name: 'app.plan.row.detail')]
    public function detail(PlanRow $row): Response
    {
        return $this->render('plan/detail.html.twig');
    }

    #[Route('/entry/{step}/{row}', name: 'app.plan.row.entry.step')]
    public function entry(int $step, PlanRow $row, Request $request): Response
    {
        $leagueConfig = $row->getPlan()->getLeague()->getLeagueConfig();
        $session = $request->getSession();
        #$session->set('entryDto', null);
        $entryDto = $session->get('entryDto');
        if(null !== $entryDto)
        {
            $dto = unserialize($entryDto);
        } else {
            $dto = new EntryDto($leagueConfig->getDoubleGames());
        }
        $options = ['leagueConfig' => $leagueConfig];
        switch($step) {
            case 1:
                $formType = EntryStep1Type::class;
                $label = 'Doppel';

                break;
            case 2:
                $label = 'Einzel';
                $formType = EntryStep2Type::class;
                break;
            default:
                throw new \InvalidArgumentException(sprintf('step %d does not exist', $step));
        }

        $form = $this->createForm($formType, $dto, $options);
        $form->handleRequest($request);
        if($form->isSubmitted() && $form->isValid())
        {
            $request->getSession()->set('entryDto', serialize($dto));
            return $this->redirectToRoute('app.plan.row.entry.step', [
                'step' => $step + 1,
                'row' => $row->getId(),
            ]);
        }

        return $this->renderForm('plan/entryStep1.html.twig', [
            'row' => $row,
            'form' => $form,
            'label' => $label,
        ]);
    }
}
