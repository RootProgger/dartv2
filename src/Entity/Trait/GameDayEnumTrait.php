<?php
declare(strict_types=1);

namespace App\Entity\Trait;
use App\Entity\League;
use App\Enum\GameDay;
use Doctrine\ORM\Mapping as ORM;

trait GameDayEnumTrait
{
    #[ORM\Column(type: 'string', nullable: true, enumType: GameDay::class)]
    private ?GameDay $day = null;

    /**
     * @return null|GameDay
     */
    public function getDay(): ?GameDay
    {
        return $this->day;
    }

    /**
     * @param GameDay|null $day
     * @return self
     */
    public function setDay(?GameDay $day): self
    {
        $this->day = $day;
        return $this;
    }
}
