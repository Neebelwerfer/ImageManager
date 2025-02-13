<?php

namespace App\DTO;

use App\Models\Traits;
use Livewire\Wireable;

class ImageTraitDTO implements Wireable
{
    protected readonly Traits $trait;
    protected readonly int $owner_id;
    protected string $value;

    public function __construct(Traits $trait, int $owner_id, string $value) {
        $this->trait = $trait;
        $this->owner_id = $owner_id;
        $this->value = $value;
    }

    public function getTrait()
    {
        return $this->trait;
    }

    public function getValue()
    {
        return $this->value;
    }

    public function setValue($value) {
        if($this->trait->type === 'boolean') {
            $this->value = $value === '1';
        }
        else if ($this->trait->type === 'float') {
            $v = floatval($value);
            if(!is_nan($v)){

                $this->value = min(max($v, $this->trait->min), $this->trait->max);
            }
        } else if ($this->trait->type === 'integer') {
            $v = intval($value);
            if(!is_nan($v))
                $this->value = min(max($v, $this->trait->min), $this->trait->max);
        }
        else {
            $this->value = $value;
        }
    }

    public function type()
    {
        return $this->trait->type;
    }

    public function toLivewire()
    {
        return [
            'trait_id' => $this->trait->id,
            'owner_id' => $this->owner_id,
            'value' => $this->value,
        ];
    }

    public static function fromLivewire($value)
    {
        $trait = Traits::owned($value['owner_id'])->find($value['trait_id']) ?? null;
        $owner_id = $value['owner_id'];
        $value = $value['value'];

        return new static($trait, $owner_id, $value);
    }
}
