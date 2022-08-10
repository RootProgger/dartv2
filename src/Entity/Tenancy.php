<?php
declare(strict_types=1);
namespace App\Entity;

use App\Repository\TenancyRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;
use JetBrains\PhpStorm\Pure;


#[ORM\Entity(repositoryClass: TenancyRepository::class)]
#[ORM\UniqueConstraint(name: 'siteUrl_idx', columns: ['site_url'])]
class Tenancy
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column()]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $siteUrl = null;

    #[ORM\Column(length: 255)]
    private ?string $siteName = null;

    #[ORM\Column(length: 255)]
    #[Gedmo\Slug(fields: ['siteName'])]
    private ?string $slug = null;

    #[ORM\Column(options: ['default' => false])]
    private bool $default = false;

    #[ORM\OneToMany(mappedBy: 'tenancy', targetEntity: Team::class, orphanRemoval: true)]
    private Collection $teams;

    #[ORM\OneToMany(mappedBy: 'tenancy', targetEntity: Place::class, orphanRemoval: true)]
    private Collection $places;

    #[ORM\OneToMany(mappedBy: 'tenancy', targetEntity: User::class)]
    private Collection $users;

    #[ORM\OneToMany(mappedBy: 'tenancy', targetEntity: League::class)]
    private Collection $leagues;

    public function __construct()
    {
        $this->teams = new ArrayCollection();
        $this->places = new ArrayCollection();
        $this->users = new ArrayCollection();
        $this->leagues = new ArrayCollection();
    }


    public function getId(): ?int
    {
        return $this->id;
    }

    public function getSiteUrl(): ?string
    {
        return $this->siteUrl;
    }

    public function setSiteUrl(string $siteUrl): self
    {
        $this->siteUrl = $siteUrl;

        return $this;
    }

    public function getSiteName(): ?string
    {
        return $this->siteName;
    }

    public function setSiteName(string $siteName): self
    {
        $this->siteName = $siteName;

        return $this;
    }


    public function getSlug(): ?string
    {
        return $this->slug;
    }


    public function setSlug(?string $slug): Tenancy
    {
        $this->slug = $slug;
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
            $team->setTenancy($this);
        }

        return $this;
    }

    public function removeTeam(Team $team): self
    {
        if ($this->teams->removeElement($team)) {
            // set the owning side to null (unless already changed)
            if ($team->getTenancy() === $this) {
                $team->setTenancy(null);
            }
        }

        return $this;
    }

    #[Pure]
    public function __toString(): string
    {
        return $this->getSiteName();
    }

    /**
     * @return Collection<int, Place>
     */
    public function getPlaces(): Collection
    {
        return $this->places;
    }

    public function addPlace(Place $place): self
    {
        if (!$this->places->contains($place)) {
            $this->places[] = $place;
            $place->setTenancy($this);
        }

        return $this;
    }

    public function removePlace(Place $place): self
    {
        if ($this->places->removeElement($place)) {
            // set the owning side to null (unless already changed)
            if ($place->getTenancy() === $this) {
                $place->setTenancy(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, User>
     */
    public function getUsers(): Collection
    {
        return $this->users;
    }

    public function addUser(User $user): self
    {
        if (!$this->users->contains($user)) {
            $this->users[] = $user;
            $user->setTenancy($this);
        }

        return $this;
    }

    public function removeUser(User $user): self
    {
        if ($this->users->removeElement($user)) {
            // set the owning side to null (unless already changed)
            if ($user->getTenancy() === $this) {
                $user->setTenancy(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, League>
     */
    public function getLeagues(): Collection
    {
        return $this->leagues;
    }

    public function addLeague(League $league): self
    {
        if (!$this->leagues->contains($league)) {
            $this->leagues[] = $league;
            $league->setTenancy($this);
        }

        return $this;
    }

    public function removeLeague(League $league): self
    {
        if ($this->leagues->removeElement($league)) {
            // set the owning side to null (unless already changed)
            if ($league->getTenancy() === $this) {
                $league->setTenancy(null);
            }
        }

        return $this;
    }

    public function isDefault(): bool
    {
        return $this->default;
    }

    public function setDefault(bool $default): Tenancy
    {
        $this->default = $default;
        return $this;
    }


}
