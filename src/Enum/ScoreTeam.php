<?php
declare(strict_types=1);

namespace App\Enum;

enum ScoreTeam: string
{
    case HOME = 'home';
    case GUEST = 'guest';
}
