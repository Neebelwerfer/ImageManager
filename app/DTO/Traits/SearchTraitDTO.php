<?php

namespace App\DTO\Traits;

use App\Models\Traits;
use Livewire\Wireable;

class SearchTraitDTO implements Wireable
{

    public readonly string $name;
    public readonly string $type;

    public function __construct(
        public readonly Traits $trait,
        public string $value,
    ) {
        $this->name = $trait->name;
        $this->type = $trait->type;
    }

    public function toLivewire()
    {
        return [
            'trait_id' => $this->trait->id,
            'value' => $this->value,
        ];
    }

    public static function fromLivewire($data)
    {
        return new static(Traits::find($data['trait_id']), $data['value']);
    }
}
