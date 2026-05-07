<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Machine;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        User::updateOrCreate(
            ['username' => 'Cashier001'],
            [
                'name'     => 'Jamaillah Santi',
                'email'    => 'jammy@gmail.com',
                'password' => Hash::make('jamjam'),
                'role'     => 'cashier',
            ]
        );

        User::updateOrCreate(
            ['username' => 'Customer001'],
            [
                'name'     => 'Germaine Joy Incinada',
                'email'    => 'begi@gmail.com',
                'password' => Hash::make('joyjoy'),
                'role'     => 'customer',
                'points'   => 0,
            ]
        );

        User::updateOrCreate(
            ['username' => 'Customer002'],
            [
                'name'     => 'Vincent Abante',
                'email'    => 'vince@gmail.com',
                'password' => Hash::make('cent2123'),
                'role'     => 'customer',
                'points'   => 1,
            ]
        );

        User::updateOrCreate(
            ['username' => 'Customer003'],
            [
                'name'     => 'Maria Santos',
                'email'    => 'maria@gmail.com',
                'password' => Hash::make('maria123'),
                'role'     => 'customer',
                'points'   => 1,
            ]
        );

        User::updateOrCreate(
            ['username' => 'Customer004'],
            [
                'name'     => 'Michael Jakston',
                'email'    => 'jakston@gmail.com',
                'password' => Hash::make('mic2626'),
                'role'     => 'customer',
                'points'   => 0,
            ]
        );

        User::updateOrCreate(
            ['username' => 'Customer005'],
            [
                'name'     => 'Ana Marie',
                'email'    => 'ana@gmail.com',
                'password' => Hash::make('ana1234'),
                'role'     => 'customer',
                'points'   => 0,
            ]
        );

        for ($i = 1; $i <= 12; $i++) {
            $num = str_pad($i, 2, '0', STR_PAD_LEFT);
            Machine::firstOrCreate(
                ['machine_number' => 'M-' . $num],
                ['type' => $i <= 6 ? 'washer' : 'dryer', 'status' => 'available']
            );
        }
    }
}