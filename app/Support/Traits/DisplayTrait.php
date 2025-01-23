<?php

namespace App\Support\Traits;

use Livewire\Wireable;

class DisplayTrait implements Wireable
{
    protected int $trait_id;
    protected string $name;
    protected string $type;
    protected string $value;

    public function display()
    {
        if($this->type === 'boolean') {
            return $this->name. ': ' . ($this->value === '1' ? 'True' : 'False');
        }

        return $this->name.': '. $this->value;
    }

    public function __construct(int $trait_id, string $name, string $type, string $value)
    {
        $this->trait_id = $trait_id;
        $this->name = $name;
        $this->type = $type;
        $this->value = $value;
    }

    public function setValue($value)
    {
        $this->value = $value;
    }

    public function toLivewire()
    {
        return [
            'trait_id' => $this->trait_id,
            'name' => $this->name,
            'type' => $this->type,
            'value' => $this->value
        ];
    }

    public static function fromLivewire($value)
    {
        $trait_id = $value['trait_id'];
        $name = $value['name'];
        $type = $value['type'];
        $value = $value['value'];
        return new static($trait_id, $name, $type, $value);
    }
}
