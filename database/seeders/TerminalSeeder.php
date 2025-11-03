<?php

namespace Database\Seeders;

use App\Models\Terminal;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class TerminalSeeder extends Seeder {

    public function run() {

        $terminal = Terminal::create([
            'name' => 'Principal',
            'numbering_range_id' => 1,
        ]);

        $users = User::all();

        foreach ($users as $user) {
            $user->terminals()->attach($terminal->id);
        }

        $terminal = Terminal::create([
            'name' => 'Caja Factro',
            'numbering_range_id' => 2,
        ]);

        $users = User::query()->where('email', 'factro.user@gmail.com');

        foreach ($users as $user) {
            $user->terminals()->attach($terminal->id);
        }

    }
}
