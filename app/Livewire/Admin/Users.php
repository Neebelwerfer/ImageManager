<?php

namespace App\Livewire\Admin;

use App\Livewire\Forms\CreateUserForm;
use App\Models\User;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Layout;
use Livewire\Component;
use Livewire\WithPagination;

#[Layout('layouts.admin')]
class Users extends Component
{
    use WithPagination;

    public CreateUserForm $userForm;


    public function createUser()
    {
        $this->userForm->submit();
    }

    #[Computed()]
    public function Users()
    {
        return User::all()->paginate(20);
    }

    public function render()
    {
        return view('livewire.admin.users',
            [
                'users' => User::all()->paginate(20),
            ]);
    }
}
