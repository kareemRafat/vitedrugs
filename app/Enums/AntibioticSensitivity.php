<?php

namespace App\Enums;

enum AntibioticSensitivity: string
{
    case Sensitive = 'sensitive';
    case Moderate = 'moderate';
    case Resistant = 'resistant';
}
