<?php
declare(strict_types=1);

namespace App\Entity;

use App\Repository\PlanRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: PlanRepository::class)]
class Plan
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column()]
    private ?int $id = null;

    #[ORM\ManyToOne(inversedBy: 'plans')]
    #[ORM\JoinColumn(nullable: false)]
    private ?League $league = null;

    #[ORM\Column]
    private ?bool $active = null;

    #[ORM\Column(type: Types::DATE_MUTABLE)]
    private ?\DateTimeInterface $startDate = null;

    #[ORM\OneToMany(mappedBy: 'plan', targetEntity: PlanRow::class, cascade: ['persist', 'remove'], orphanRemoval: true)]
    private Collection $planRows;

    //virtual Easyadmin
    private ?\DateTimeInterface $pauseStart = null;
    private ?int $pauseLength = null;

    public function __construct()
    {
        $this->planRows = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getLeague(): ?League
    {
        return $this->league;
    }

    public function setLeague(?League $league): self
    {
        $this->league = $league;

        return $this;
    }

    public function isActive(): ?bool
    {
        return $this->active;
    }

    public function setActive(bool $active): self
    {
        $this->active = $active;

        return $this;
    }

    public function getStartDate(): ?\DateTimeInterface
    {
        return $this->startDate;
    }

    public function setStartDate(\DateTimeInterface $startDate): self
    {
        $this->startDate = $startDate;

        return $this;
    }

    /**
     * @return Collection<int, PlanRow>
     */
    public function getPlanRows(): Collection
    {
        return $this->planRows;
    }

    public function addPlanRow(PlanRow $planRow): self
    {
        if (!$this->planRows->contains($planRow)) {
            $this->planRows[] = $planRow;
            $planRow->setPlan($this);
        }

        return $this;
    }

    public function removePlanRow(PlanRow $planRow): self
    {
        if ($this->planRows->removeElement($planRow)) {
            // set the owning side to null (unless already changed)
            if ($planRow->getPlan() === $this) {
                $planRow->setPlan(null);
            }
        }

        return $this;
    }

    /**
     * @return \DateTimeInterface|null
     */
    public function getPauseStart(): ?\DateTimeInterface
    {
        return $this->pauseStart;
    }

    /**
     * @param \DateTimeInterface|null $pauseStart
     * @return Plan
     */
    public function setPauseStart(?\DateTimeInterface $pauseStart): Plan
    {
        $this->pauseStart = $pauseStart;
        return $this;
    }

    /**
     * @return int|null
     */
    public function getPauseLength(): ?int
    {
        return $this->pauseLength;
    }

    /**
     * @param int|null $pauseLength
     * @return Plan
     */
    public function setPauseLength(?int $pauseLength): Plan
    {
        $this->pauseLength = $pauseLength;
        return $this;
    }

    /**
     * @return array|PlanRow[]
     */
    public function getOrderedByGameday(): array
    {
        $plan = [];
        foreach ($this->getPlanRows() as $row)
        {
            $plan[$row->getGameDay()][] = $row;
        }

        ksort($plan);

        return $plan;
    }
}
