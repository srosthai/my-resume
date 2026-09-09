<?php

namespace App\Enums;

enum ProjectStatus: string
{
    case Processing = 'processing';
    case Completed = 'completed';
}
