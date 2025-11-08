<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Contest;
use App\Models\Participation;

class FakeLeaderboardSeeder extends Seeder
{
    public function run(): void
    {
        // Create a test contest if not exists
        $contest = Contest::first() ?? Contest::factory()->create([
            'name' => 'Leaderboard Demo Contest',
            'description' => 'Used for testing pagination and leaderboard.',
            'access_level' => 'normal',
            'start_at' => now(),
            'end_at' => now()->addDays(2),
            'status' => 'live',
            'prize_details' => ['type' => 'Voucher', 'value' => '₹1000'],
            'created_by' => 1,
        ]);

        // Create 50 users
        User::factory(50)->create();

        // Create 50 participations with random scores
        Participation::factory(50)->create([
            'contest_id' => $contest->id,
            'status' => 'submitted',
        ]);
    }
}
