<?php
declare(strict_types=1);
namespace App\Entity;

use App\Repository\PlayersRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use JetBrains\PhpStorm\Pure;

#[ORM\Entity(repositoryClass: PlayersRepository::class)]
class Players
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column()]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $firstname = null;

    #[ORM\Column(length: 255)]
    private ?string $lastname = null;

    #[ORM\ManyToOne(inversedBy: 'players')]
    private ?Team $team = null;

    #[ORM\ManyToOne]
    #[ORM\JoinColumn(nullable: false)]
    private ?Tenancy $tenancy = null;

    #[ORM\OneToMany(mappedBy: 'player', targetEntity: PlayerScore::class, orphanRemoval: true)]
    private Collection $playerScores;

    public function __construct()
    {
        $this->playerScores = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getFirstname(): ?string
    {
        return $this->firstname;
    }

    public function setFirstname(string $firstname): self
    {
        $this->firstname = $firstname;

        return $this;
    }

    public function getLastname(): ?string
    {
        return $this->lastname;
    }

    public function setLastname(string $lastname): self
    {
        $this->lastname = $lastname;

        return $this;
    }

    public function getTeam(): ?Team
    {
        return $this->team;
    }

    public function setTeam(?Team $team): self
    {
        $this->team = $team;

        return $this;
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
     * @return Collection<int, PlayerScore>
     */
    public function getPlayerScores(): Collection
    {
        return $this->playerScores;
    }

    public function addPlayerScore(PlayerScore $playerScore): self
    {
        if (!$this->playerScores->contains($playerScore)) {
            $this->playerScores->add($playerScore);
            $playerScore->setPlayer($this);
        }

        return $this;
    }

    public function removePlayerScore(PlayerScore $playerScore): self
    {
        if ($this->playerScores->removeElement($playerScore)) {
            // set the owning side to null (unless already changed)
            if ($playerScore->getPlayer() === $this) {
                $playerScore->setPlayer(null);
            }
        }

        return $this;
    }

    #[Pure] public function __toString(): string
    {
        return $this->getLastname() . ' ' . $this->getFirstname();
    }
}
