<?php

namespace App\Support\Traits;

use App\Models\Traits;
use Livewire\Wireable;

class AddedTrait implements Wireable
{
    protected Traits $trait;
    protected string $value;

    public function __construct(Traits $trait, string $value) {
        $this->trait = $trait;
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
            'value' => $this->value,
        ];
    }

    public static function fromLivewire($value)
    {
        $trait = Traits::personalOrGlobal()->find($value['trait_id']) ?? null;
        $value = $value['value'];

        return new static($trait, $value);
    }
}
