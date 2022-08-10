<?php

namespace App\Enum;

enum GameDay: string
{
    case Montag = 'monday';
    case Dienstag = 'tuesday';
    case Mittwoch = 'wednesday';
    case Donnerstag = 'thursday';
    case Freitag = 'friday';
    case Samstag = 'saturday';
    case Sonntag = 'sunday';
}
