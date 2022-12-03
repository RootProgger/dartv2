<?php
declare(strict_types=1);
namespace App\Entity;

use App\Entity\Trait\GameDayEnumTrait;
use App\Repository\LeagueRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\Common\Collections\Criteria;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use JetBrains\PhpStorm\Pure;


#[ORM\Entity(repositoryClass: LeagueRepository::class)]
class League
{
    use GameDayEnumTrait;

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column()]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $name = null;

    #[ORM\ManyToOne(targetEntity: LeagueConfig::class)]
    #[ORM\JoinColumn(nullable: false)]
    private ?LeagueConfig $leagueConfig = null;

    #[ORM\OneToMany(mappedBy: 'league', targetEntity: Team::class)]
    private Collection $teams;

    #[ORM\ManyToOne(inversedBy: 'leagues')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Tenancy $tenancy = null;

    #[ORM\OneToMany(mappedBy: 'league', targetEntity: Plan::class, orphanRemoval: true)]
    private Collection $plans;

    #[ORM\Column(type: Types::SMALLINT, nullable: true)]
    private ?int $sort = null;


    #[Pure] public function __construct()
    {
        $this->teams = new ArrayCollection();
        $this->plans = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
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

    public function getLeagueConfig(): ?LeagueConfig
    {
        return $this->leagueConfig;
    }

    public function setLeagueConfig(?LeagueConfig $leagueConfig): self
    {
        $this->leagueConfig = $leagueConfig;

        return $this;
    }

    /**
     * @return Collection<int, Team>
     */
    public function getTeams(): Collection
    {
        return $this->teams;
    }

    public function addTeam(Team $team): self
    {
        if (!$this->teams->contains($team)) {
            $this->teams[] = $team;
            $team->setLeague($this);
        }

        return $this;
    }

    public function removeTeam(Team $team): self
    {
        if ($this->teams->removeElement($team)) {
            // set the owning side to null (unless already changed)
            if ($team->getLeague() === $this) {
                $team->setLeague(null);
            }
        }

        return $this;
    }

    public function __toString(): string
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
     * @return Collection<int, Plan>
     */
    public function getPlans(): Collection
    {
        return $this->plans;
    }

    public function getFirstActivePlan(): bool|Plan
    {
        $criteria = Criteria::create();
        $criteria
            ->where(Criteria::expr()->eq('active', true))
            ;
        return $this->plans->matching($criteria)->first();
    }

    public function addPlan(Plan $plan): self
    {
        if (!$this->plans->contains($plan)) {
            $this->plans[] = $plan;
            $plan->setLeague($this);
        }

        return $this;
    }

    public function removePlan(Plan $plan): self
    {
        if ($this->plans->removeElement($plan)) {
            // set the owning side to null (unless already changed)
            if ($plan->getLeague() === $this) {
                $plan->setLeague(null);
            }
        }

        return $this;
    }

    public function getSort(): ?int
    {
        return $this->sort;
    }

    public function setSort(?int $sort): self
    {
        $this->sort = $sort;

        return $this;
    }
}
