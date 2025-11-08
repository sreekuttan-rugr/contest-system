<?php

namespace Database\Seeders;

use App\Models\Participation;
use App\Models\User;
use App\Models\Contest;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ParticipationSeeder extends Seeder
{
    public function run(): void
    {
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        Participation::truncate();
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');

        $users = User::whereIn('role', ['user', 'vip'])->get();
        $contests = Contest::all();

        foreach ($users as $user) {
            foreach ($contests as $contest) {
                Participation::create([
                    'user_id' => $user->id,
                    'contest_id' => $contest->id,
                    'started_at' => now(),
                    'answers' => [],
                    'score' => 0,
                    'status' => 'in_progress',
                ]);
            }
        }
    }
}
