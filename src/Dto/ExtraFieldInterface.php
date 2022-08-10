<?php
declare(strict_types=1);

namespace App\Dto;

interface ExtraFieldInterface
{
    public function getName(): string;
    public function getValue(): float;
}
