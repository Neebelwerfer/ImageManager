<?php

namespace App\Support\Enums;

enum TraitTypes: string
{
    case Integer = 'integer';
    case Float = 'float';
    case Boolean = 'boolean';
    case Text = 'text';
    case Date = 'date';
    case Time = 'time';
    case DateTime = 'datetime';
}
