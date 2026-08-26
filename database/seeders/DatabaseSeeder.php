<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        User::factory()->manager()->create([
            'username' => 'admin',
            'email' => 'admin@fatherShope.com',
            'password' => Hash::make('password'),
        ]);

        User::factory()->create([
            'username' => 'cashier',
            'email' => 'cashier@fatherShope.com',
            'password' => Hash::make('password'),
        ]);

        $this->call(ProductSeeder::class);
        $this->call(SaleSeeder::class);

        $this->command->info('--- Login Credentials ---');
        $this->command->info('Manager: admin@fatherShope.com / password');
        $this->command->info('Cashier: cashier@fatherShope.com / password');
    }
}
