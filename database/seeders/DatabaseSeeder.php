<?php

namespace Database\Seeders;

use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // User::factory(10)->create();

        $j = new User();
        $j->name = 'Jakob';
        $j->email = 't@t.t';
        $j->is_admin = true;
        $j->password = bcrypt('123');
        $j->save();

        $z = new User();
        $z->name = 'Zainab';
        $z->email = 'z@z.z';
        $z->is_admin = false;
        $z->password = bcrypt('123');
        $z->save();
    }
}
