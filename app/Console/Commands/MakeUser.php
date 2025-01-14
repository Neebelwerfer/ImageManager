<?php

namespace App\Console\Commands;

use App\Models\User;
use Illuminate\Console\Command;

class MakeUser extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'user:make';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $user = new User();
        $user->name = $this->ask('Name');
        $user->email = $this->ask('Email');
        $user->is_admin = $this->confirm('Is admin?');
        $user->password = bcrypt($this->secret('Password'));
        $user->save();
    }
}
