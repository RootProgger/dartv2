<?php
declare(strict_types=1);

namespace App\Dto;


class ExtraField implements ExtraFieldInterface
{
    public string $name;
    public float  $value;

    public function getName(): string
    {
        return $this->name;
    }

    public function getValue(): float
    {
        return $this->value;
    }
}
