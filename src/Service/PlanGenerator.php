<?php
declare(strict_types=1);
namespace App\Service;

use App\Entity\Plan;
use App\Entity\PlanRow;
use App\Entity\Team;
use App\Enum\GameDay;
use Doctrine\ORM\EntityManagerInterface;

final class PlanGenerator
{

    public function __construct(private EntityManagerInterface $entityManager) {}

    public function addPause(Plan $plan): void
    {
        if(null === $plan->getPauseStart() || null === $plan->getPauseLength())
        {
            throw new \InvalidArgumentException('Plan-Pause or Length not set');
        }

        $intervalToAdd = new \DateInterval(sprintf('P%dW', $plan->getPauseLength()));

        /** @var PlanRow[] $rows */
        $rows = $this->entityManager->getRepository(PlanRow::class)->findGreaterEqualsThanDate($plan, $plan->getPauseStart());

        foreach($rows as $row) {
            $newDate = clone $row->getDate();
            $newDate->add($intervalToAdd);
            $row->setDate($newDate);
        }

        $this->entityManager->flush();
    }

    public function generate(Plan $plan): Plan
    {
        $league = $plan->getLeague();
        $teams = $league->getTeams();

        $teamCount = $teams->count();
        if($teamCount < 4) {
            throw new \LengthException(sprintf('Teamcount %d is lower than 4 ', $teamCount));
        }



        $teams = $teams->toArray();
        if($teamCount % 2) {

            $teams[] = null;
            $teamCount++;
        }

        shuffle($teams);

        $xpos  = $teamCount - 1; // höchster Key im Array
        $days  = $teamCount - 1; // Anzahl der Spieltage pro Runde
        $pairs = $teamCount / 2; // Anzahl der möglichen Spielpaare
        $games = $pairs * $days; // Anzahl der Spiele pro Hin-/Rück-Runde
        $gameNr=0;

        for($day = 1; $day < $teamCount; $day++)
        {
            $backRound = $day + $days;

            array_splice($teams, 1, 1, [array_pop($teams), $teams[1]]);
            for($spPair=0;$spPair < $pairs; $spPair++)
            {

                $gameNr++;

                if($gameNr % $teamCount !== 1 && $spPair % 2 === 0)
                {
                    $home  = $teams[$spPair];
                    $guest = $teams[$xpos - $spPair];
                } else {
                    $home  = $teams[$xpos - $spPair];
                    $guest = $teams[$spPair];
                }
                if(null === $league->getDay() && null !== $home && null === $home->getDay())
                {
                    throw new \DomainException(sprintf('Team: "%s" has no Gameday set. League Gameday overwrites Team-Gameday!', $home->getName()));
                }

                $planRow = (new PlanRow())
                    ->setGameDay($day)
                    ->setHomeTeam($home)
                    ->setGuestTeam($guest)
                    ;

                $planRowBack = (new PlanRow())
                    ->setGameDay($backRound)
                    ->setGuestTeam($home)
                    ->setHomeTeam($guest)
                    ;
                $plan
                    ->addPlanRow($planRow)
                    ->addPlanRow($planRowBack)
                    ;
            }
        }

        $startDate = $plan->getStartDate();

        foreach ($plan->getPlanRows() as $row)
        {
            $gameDay = null;
            if(null !== $row->getHomeTeam() && null !== $row->getHomeTeam()->getDay())
            {
                $gameDay = $row->getHomeTeam()->getDay();
            }
            // liga überschreibt team spieltag
            if(null !== $league->getDay()) {
                $gameDay = $league->getDay();
            }

            $weekday = 0; //monday
            switch ($gameDay) {
                case GameDay::Dienstag:
                    $weekday += 1;
                    break;
                case GameDay::Mittwoch:
                    $weekday += 2;
                    break;
                case GameDay::Donnerstag:
                    $weekday += 3;
                    break;
                case GameDay::Freitag:
                    $weekday += 4;
                    break;
                case GameDay::Samstag:
                    $weekday += 5;
                    break;
                case GameDay::Sonntag:
                    $weekday += 6;
                    break;
            }


            $gameDate = clone $startDate;
            $gameDate->add(new \DateInterval(sprintf('P%dD', $weekday)));

            $interval = $row->getGameDay() -1;
            $gameDate->add(new \DateInterval(sprintf('P%dW', $interval)));

            if(null !== $plan->getPauseStart() && null !== $plan->getPauseLength())
            {
                if($gameDate >= $plan->getPauseStart())
                {
                    $gameDate->add(new \DateInterval(sprintf('P%dW', $plan->getPauseLength())));
                }
            }

            $row->setDate($gameDate);

        }

        return $plan;
    }


}
