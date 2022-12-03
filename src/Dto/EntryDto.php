<?php

namespace App\Dto;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Validator\Context\ExecutionContextInterface;

#[Assert\Callback('validate', groups: ['step1'])]
class EntryDto
{
    private const SERIALIZE_KEY_DOUBLESHOME = 'doublesHome';
    private const SERIALIZE_KEY_DOUBLESGUEST = 'doublesGuest';
    private const SERIALIZE_KEY_DOUBLEGAMES = 'doubleGames';

    private int|null $doublesHome;
    private int|null $doublesGuest;
    private int|null $singlesHome;
    private int |null $singlesGuest;
    private int $games;

    public function __construct(int $games){
        $this->games = $games;
    }

    public function validate(ExecutionContextInterface $context)
    {
        if($this->getDoublesHome() + $this->getDoublesGuest() <> $this->games)
        {
            $context->buildViolation(sprintf('Doppelspiele dürfen addiert %d ergeben', $this->games))
                ->addViolation();
        }
    }


    public function __serialize()
    {
        return [
            self::SERIALIZE_KEY_DOUBLESHOME => $this->doublesHome,
            self::SERIALIZE_KEY_DOUBLESGUEST => $this->doublesGuest,
            self::SERIALIZE_KEY_DOUBLEGAMES => $this->games,
        ];
    }

    public function __unserialize(array $data)
    {
        return [
            self::SERIALIZE_KEY_DOUBLESHOME => $this->doublesHome,
            self:: SERIALIZE_KEY_DOUBLESGUEST => $this->doublesGuest,
            self::SERIALIZE_KEY_DOUBLEGAMES => $this->games,
            ] = $data;
    }

    public function getDoublesHome(): ?int
    {
        return $this->doublesHome;
    }

    public function setDoublesHome(?int $doublesHome): EntryDto
    {
        $this->doublesHome = $doublesHome;
        return $this;
    }

    public function getDoublesGuest(): ?int
    {
        return $this->doublesGuest;
    }

    public function setDoublesGuest(?int $doublesGuest): EntryDto
    {
        $this->doublesGuest = $doublesGuest;
        return $this;
    }

    /**
     * @return int|null
     */
    public function getSinglesHome(): ?int
    {
        return $this->singlesHome;
    }

    /**
     * @param int|null $singlesHome
     * @return EntryDto
     */
    public function setSinglesHome(?int $singlesHome): EntryDto
    {
        $this->singlesHome = $singlesHome;
        return $this;
    }

    /**
     * @return int|null
     */
    public function getSinglesGuest(): ?int
    {
        return $this->singlesGuest;
    }

    /**
     * @param int|null $singlesGuest
     * @return EntryDto
     */
    public function setSinglesGuest(?int $singlesGuest): EntryDto
    {
        $this->singlesGuest = $singlesGuest;
        return $this;
    }

}
