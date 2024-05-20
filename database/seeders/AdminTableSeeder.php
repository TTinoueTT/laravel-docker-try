<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Admin;
use Illuminate\Support\Facades\Hash;

class AdminTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Admin::factory()->create([
            'name' => 'sano',
            'login_id' => 'sano_id',
            'password' => Hash::make('hogehoge'),
        ]);

        Admin::factory()->create([
            'name' => 'sato',
            'login_id' => 'sato_id',
            'password' => Hash::make('hogehoge'),
        ]);
    }
}
