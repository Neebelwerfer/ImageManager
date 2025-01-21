<?php

namespace App\Livewire\Manage;

use App\Models\Traits as TraitsModel;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Layout;
use Livewire\Component;
use Livewire\WithPagination;

#[Layout('layouts.manage')]
class Traits extends Component
{
    use WithPagination;

    public $listeners = [
        'refresh' => '$refresh'
    ];

    public function deleteTrait($id)
    {
        $trait = TraitsModel::find($id);
        if(isset($trait)) {
            if($trait->global && !Auth::user()->is_admin) {
                return;
            }

            if(!$trait->global && $trait->owner_id != Auth::user()->id) {
                return;
            }

            $trait->delete();
        }
    }

    public function render()
    {
        return view('livewire.manage.traits',
            [
                'globalTraits' => TraitsModel::global()->paginate(20),
                'traits' => TraitsModel::personal()->paginate(20),
            ]);
    }
}
