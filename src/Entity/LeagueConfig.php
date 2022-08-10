<?php
declare(strict_types=1);
namespace App\Entity;

use App\Repository\LeagueConfigRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use phpDocumentor\Reflection\Type;

#[ORM\Entity(repositoryClass: LeagueConfigRepository::class)]
class LeagueConfig
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column()]
    private ?int $id = null;

    #[ORM\Column]
    private ?bool $teamgame = null;

    #[ORM\Column]
    private ?int $pointsWin = null;

    #[ORM\Column]
    private ?int $pointsLost = null;

    #[ORM\Column]
    private ?int $pointsTie = null;

    #[ORM\Column]
    private ?int $singleGames = null;

    #[ORM\Column]
    private ?int $doubleGames = null;

    #[ORM\Column(length: 255)]
    private ?string $name = null;

    #[ORM\ManyToOne]
    #[ORM\JoinColumn(nullable: false)]
    private ?Tenancy $tenancy = null;

    #[ORM\Column(nullable: true)]
    private ?array $extraFields = [];

    public function getId(): ?int
    {
        return $this->id;
    }

    public function isTeamgame(): ?bool
    {
        return $this->teamgame;
    }

    public function setTeamgame(bool $teamgame): self
    {
        $this->teamgame = $teamgame;

        return $this;
    }

    public function getPointsWin(): ?int
    {
        return $this->pointsWin;
    }

    public function setPointsWin(int $pointsWin): self
    {
        $this->pointsWin = $pointsWin;

        return $this;
    }

    public function getPointsLost(): ?int
    {
        return $this->pointsLost;
    }

    public function setPointsLost(int $pointsLost): self
    {
        $this->pointsLost = $pointsLost;

        return $this;
    }

    public function getPointsTie(): ?int
    {
        return $this->pointsTie;
    }

    public function setPointsTie(int $pointsTie): self
    {
        $this->pointsTie = $pointsTie;

        return $this;
    }

    public function getSingleGames(): ?int
    {
        return $this->singleGames;
    }

    public function setSingleGames(int $singleGames): self
    {
        $this->singleGames = $singleGames;

        return $this;
    }

    public function getDoubleGames(): ?int
    {
        return $this->doubleGames;
    }

    public function setDoubleGames(int $doubleGames): self
    {
        $this->doubleGames = $doubleGames;

        return $this;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): self
    {
        $this->name = $name;

        return $this;
    }

    public function __toString()
    {
        return $this->name;
    }

    public function getTenancy(): ?Tenancy
    {
        return $this->tenancy;
    }

    public function setTenancy(?Tenancy $tenancy): self
    {
        $this->tenancy = $tenancy;

        return $this;
    }

    /**
     * @return array|null
     */
    public function getExtraFields(): ?array
    {
        return $this->extraFields;
    }

    /**
     * @param array|null $extraFields
     * @return LeagueConfig
     */
    public function setExtraFields(?array $extraFields): LeagueConfig
    {
        $this->extraFields = $extraFields;
        return $this;
    }
}
