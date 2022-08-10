<?php
declare(strict_types=1);

namespace App\Entity;

use App\Repository\PlayerScoreRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: PlayerScoreRepository::class)]
class PlayerScore
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column()]
    private ?int $id = null;

    #[ORM\Column(type: Types::SMALLINT)]
    private ?int $gamesWin = null;

    #[ORM\Column]
    private array $extraFields = [];

    #[ORM\ManyToOne(inversedBy: 'playerScore')]
    #[ORM\JoinColumn(nullable: false)]
    private ?PlanRow $planRow = null;

    #[ORM\ManyToOne(inversedBy: 'playerScores')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Players $player = null;


    public function getId(): ?int
    {
        return $this->id;
    }

    public function getExtraFields(): array
    {
        return $this->extraFields;
    }

    public function setExtraFields(array $extraFields): self
    {
        $this->extraFields = $extraFields;

        return $this;
    }

    public function getGamesWin(): ?int
    {
        return $this->gamesWin;
    }

    public function setGamesWin(int $gamesWin): self
    {
        $this->gamesWin = $gamesWin;

        return $this;
    }

    public function getPlanRow(): ?PlanRow
    {
        return $this->planRow;
    }

    public function setPlanRow(?PlanRow $planRow): self
    {
        $this->planRow = $planRow;

        return $this;
    }

    public function getPlayer(): ?Players
    {
        return $this->player;
    }

    public function setPlayer(?Players $player): self
    {
        $this->player = $player;

        return $this;
    }
}
