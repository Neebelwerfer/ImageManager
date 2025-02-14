<?php

namespace App\Services;

use App\Jobs\Traits\CreateTrait;
use App\Models\Traits;
use App\Models\User;

class TraitService
{
    public function __construct() {
        //
    }

    public function Create(User $user, array $data)
    {
        CreateTrait::dispatch($user, $data);
    }

    public function getValidationRules(Traits $trait)
    {
        $type = $trait->type;
        if($type == 'integer' || $type == 'float')
        {
            return 'required|min:'.$trait->min.'|max:'.$trait->max.'|'.($type == 'integer' ? 'integer' : 'numeric');
        }
        else if($type == 'boolean')
        {
            return 'required|';
        }
        else if($type == 'text')
        {
            return 'required|string|min:'.$trait->min.'|max:'.$trait->max;
        }
    }
}

