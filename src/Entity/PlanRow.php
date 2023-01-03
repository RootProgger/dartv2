<?php
declare(strict_types=1);
namespace App\Entity;

use App\Enum\ScoreTeam;
use App\Repository\PlanRowRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use JetBrains\PhpStorm\Pure;

#[ORM\Entity(repositoryClass: PlanRowRepository::class)]
class PlanRow
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column()]
    private ?int $id = null;


    #[ORM\Column]
    private ?int $gameDay = null;

    #[ORM\ManyToOne]
    private ?Team $homeTeam = null;

    #[ORM\ManyToOne]
    private ?Team $guestTeam = null;

    #[ORM\Column(nullable: true)]
    private ?float $pointsHome = null;

    #[ORM\Column(nullable: true)]
    private ?float $pointsGuest = null;

    #[ORM\ManyToOne(inversedBy: 'planRows')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Plan $plan = null;

    #[ORM\Column(type: Types::DATE_MUTABLE)]
    private ?\DateTimeInterface $date = null;

    #[ORM\OneToMany(mappedBy: 'planRow', targetEntity: PlayerScore::class, orphanRemoval: true)]
    private Collection $playerScore;

    #[ORM\Column(type: Types::SMALLINT, nullable: true)]
    private int|null $doubleHome;

    #[ORM\Column(type: Types::SMALLINT, nullable: true)]
    private int|null $doubleGuest;

    #[Pure] public function __construct()
    {
        $this->playerScore = new ArrayCollection();
    }


    public function getId(): ?int
    {
        return $this->id;
    }

    public function getGameDay(): ?int
    {
        return $this->gameDay;
    }

    public function setGameDay(int $gameDay): self
    {
        $this->gameDay = $gameDay;

        return $this;
    }

    public function getHomeTeam(): ?Team
    {
        return $this->homeTeam;
    }

    public function setHomeTeam(?Team $homeTeam): self
    {
        $this->homeTeam = $homeTeam;

        return $this;
    }

    public function getGuestTeam(): ?Team
    {
        return $this->guestTeam;
    }

    public function setGuestTeam(?Team $guestTeam): self
    {
        $this->guestTeam = $guestTeam;

        return $this;
    }

    public function getPointsHome(): ?float
    {
        return $this->pointsHome;
    }

    public function setPointsHome(?float $pointsHome): self
    {
        $this->pointsHome = $pointsHome;

        return $this;
    }

    public function getPointsGuest(): ?float
    {
        return $this->pointsGuest;
    }

    public function setPointsGuest(?float $pointsGuest): self
    {
        $this->pointsGuest = $pointsGuest;

        return $this;
    }

    public function getPlan(): ?Plan
    {
        return $this->plan;
    }

    public function setPlan(?Plan $plan): self
    {
        $this->plan = $plan;

        return $this;
    }

    public function getDate(): ?\DateTimeInterface
    {
        return $this->date;
    }

    public function setDate(\DateTimeInterface $date): self
    {
        $this->date = $date;

        return $this;
    }

    /**
     * @return Collection<int, PlayerScore>
     */
    public function getPlayerScore(): Collection
    {
        return $this->playerScore;
    }

    public function addPlayerScore(PlayerScore $playerScore): self
    {
        if (!$this->playerScore->contains($playerScore)) {
            $this->playerScore->add($playerScore);
            $playerScore->setPlanRow($this);
        }

        return $this;
    }

    public function removePlayerScore(PlayerScore $playerScore): self
    {
        if ($this->playerScore->removeElement($playerScore)) {
            // set the owning side to null (unless already changed)
            if ($playerScore->getPlanRow() === $this) {
                $playerScore->setPlanRow(null);
            }
        }

        return $this;
    }

    public function getHomeSumGames(): int {
        $games = 0;
        /** @var PlayerScore $score */
        foreach ($this->playerScore as $score) {
            if($score->getPlayer()->getTeam() === $this->homeTeam) {
                $games += $score->getGamesWin();
            }
        }
        return $games;
    }
    public function getGuestSumGames(): int {
        $games = 0;
        /** @var PlayerScore $score */
        foreach ($this->playerScore as $score) {
            if($score->getPlayer()->getTeam()=== $this->getGuestTeam()) {
                $games += $score->getGamesWin();
            }
        }
        return $games;
    }

    public function getDoubleHome(): int|null
    {
        return $this->doubleHome;
    }

    public function setDoubleHome(int|null $doubleHome): PlanRow
    {
        $this->doubleHome = $doubleHome;
        return $this;
    }


    public function getDoubleGuest(): int|null
    {
        return $this->doubleGuest;
    }

    public function setDoubleGuest(int|null $doubleGuest): PlanRow
    {
        $this->doubleGuest = $doubleGuest;
        return $this;
    }

    public function __toString()
    {
        return sprintf(
            'Spieltag %d, %s vs %s',
            $this->gameDay,
            $this->homeTeam->getName(),
            $this->guestTeam->getName(),
        );
    }

}
