<?php

namespace Database\Seeders;

use App\Models\Question;
use App\Models\Contest;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class QuestionSeeder extends Seeder
{
    public function run(): void
    {
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        Question::truncate();
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');

        $contest = Contest::firstOrFail();

        Question::create([
            'contest_id' => $contest->id,
            'type' => 'single',
            'question_text' => 'Which language is used for Laravel?',
            'options' => ['A' => 'PHP', 'B' => 'Python', 'C' => 'Java'],
            'correct_answers' => ['A'],
            'marks' => 1,
        ]);

        Question::create([
            'contest_id' => $contest->id,
            'type' => 'true_false',
            'question_text' => 'NestJS is built on top of Express.js.',
            'options' => ['A' => 'True', 'B' => 'False'],
            'correct_answers' => ['A'],
            'marks' => 1,
        ]);

        Question::create([
            'contest_id' => $contest->id,
            'type' => 'multi',
            'question_text' => 'Select all frontend frameworks.',
            'options' => ['A' => 'React', 'B' => 'Laravel', 'C' => 'Vue', 'D' => 'Django'],
            'correct_answers' => ['A', 'C'],
            'marks' => 2,
        ]);
    }
}
