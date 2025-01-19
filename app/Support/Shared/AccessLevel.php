<?php

namespace App\Support\Shared;

enum AccessLevel: string
{
    case view = 'view';
    case edit = 'edit';
}
