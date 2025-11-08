<?php

namespace Database\Seeders;

use App\Models\Contest;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ContestSeeder extends Seeder
{
    public function run(): void
    {
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        Contest::truncate();
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');

        $admin = User::where('role', 'admin')->firstOrFail();

        Contest::create([
            'name' => 'Tech Trivia Challenge',
            'description' => 'A contest for testing your coding IQ.',
            'access_level' => 'normal',
            'start_at' => now(),
            'end_at' => now()->addDays(2),
            'prize_details' => ['type' => 'Gift Card', 'value' => '₹1000'],
            'status' => 'live',
            'created_by' => $admin->id,
        ]);

        Contest::create([
            'name' => 'VIP Exclusive Quiz',
            'description' => 'Exclusive for VIP users only.',
            'access_level' => 'vip',
            'start_at' => now(),
            'end_at' => now()->addDays(2),
            'prize_details' => ['type' => 'Voucher', 'value' => '₹2000'],
            'status' => 'live',
            'created_by' => $admin->id,
        ]);
    }
}
