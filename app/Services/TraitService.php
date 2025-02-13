<?php

namespace App\Services;

use App\Jobs\Traits\CreateTrait;
use App\Models\User;
use Illuminate\Support\Str;

class TraitService
{
    public function __construct() {
        //
    }

    public function Create(User $user, array $data)
    {
        CreateTrait::dispatch($user, $data);
    }
}

