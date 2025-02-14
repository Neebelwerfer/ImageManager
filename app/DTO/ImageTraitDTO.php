<?php

namespace App\DTO;

use App\Models\ImageTraits;
use App\Models\Traits;
use Livewire\Wireable;

use function PHPUnit\Framework\isNan;

class ImageTraitDTO implements Wireable
{
    protected readonly Traits $trait;
    protected readonly ?ImageTraits $imageTrait;
    protected readonly int $owner_id;
    protected string $value;

    public function __construct(Traits $trait, int $owner_id, string $value, ImageTraits $imageTraits = null) {
        $this->trait = $trait;
        $this->owner_id = $owner_id;
        $this->value = $value;
        $this->imageTrait = $imageTraits;
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

    public function ImageTrait()
    {
        return $this->imageTrait;
    }

    public function display()
    {
        if($this->trait->type === 'boolean') {
            return $this->trait->name. ': ' . ($this->value === '1' ? 'True' : 'False');
        }

        return $this->trait->name.': '. $this->value;
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
            'imageTrait_id' => $this->imageTrait !== null ? $this->imageTrait->id : null
        ];
    }

    public static function fromLivewire($data)
    {
        $trait = Traits::owned($data['owner_id'])->find($data['trait_id']) ?? null;
        $owner_id = $data['owner_id'];

        $imageTrait_id = $data['imageTrait_id'];
        $imageTrait = null;
        if(!is_nan($imageTrait_id))
        {
            $imageTrait = ImageTraits::find($imageTrait_id);
        }
        $value = $data['value'];

        return new static($trait, $owner_id, $value, $imageTrait);
    }
}
