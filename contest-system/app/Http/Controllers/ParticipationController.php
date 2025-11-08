<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Participation;
use App\Models\Contest;
use App\Models\Question;


class ParticipationController extends Controller
{
    public function join(Request $request, $contestId)
    {
        $user = $request->user();
        $contest = Contest::findOrFail($contestId);

        // Check role access
        if ($contest->access_level === 'vip' && $user->role !== 'vip' && $user->role !== 'admin') {
            return response()->json(['message' => 'VIP only contest'], 403);
        }
        if (now()->lt($contest->start_at) || now()->gt($contest->end_at)) {
            return response()->json(['message' => 'Contest not active'], 403);
        }

        $participation = Participation::firstOrCreate([
            'user_id' => $user->id,
            'contest_id' => $contest->id,
        ], ['started_at' => now()]);

        return response()->json(['participation' => $participation]);
    }

    public function showQuestions(Request $request, $id)
    {
        $participation = Participation::find($id);

        // Check participation validity
        if (
            !$participation ||
            $participation->user_id !== $request->user()->id ||
            $participation->status !== 'in_progress'
        ) {
            return response()->json(['message' => 'Participation record not found or not active'], 404);
        }

        // Get the contest
        $contest = $participation->contest;

        // VIP check (if applicable)
        if ($contest->access_level === 'vip' && !$request->user()->hasRole('vip')) {
            return response()->json(['message' => 'This contest is only available for VIP users'], 403);
        }

        // Fetch only necessary fields from related questions
        $questions = Question::where('contest_id', $contest->id)
            ->select('id', 'question_text', 'type', 'options', 'marks')
            ->get();

        // Since options are stored as JSON, Laravel's $casts will return arrays automatically.
        // But if not cast, you can decode manually:
        $questions->each(function ($q) {
            if (is_string($q->options)) {
                $q->options = json_decode($q->options, true);
            }
        });

        return response()->json([
            'contest' => $contest->name,
            'questions' => $questions,
        ]);
    }


    public function saveAnswers(Request $request, $id)
    {
        // Find the participation
        $participation = Participation::find($id);
        if (!$participation || $participation->user_id !== $request->user()->id || $participation->status !== 'in_progress') {
            return response()->json(['message' => 'Participation record not found'], 404);
        }


        // Get the contest
        $contest = $participation->contest;
        if (!$contest) {
            return response()->json(['message' => 'Contest not found'], 404);
        }

        // VIP contest check
        if ($contest->access_level === 'vip' && !$request->user()->hasRole('vip')) {
            return response()->json(['message' => 'This contest is only available for VIP users'], 403);
        }

        // Validate request
        $request->validate([
            'answers' => 'required|array',
            'answers.*.question_id' => 'required|integer',
            'answers.*.selected_options' => 'required|array|min:1'
        ]);

        // Validate each answer against the question type
        foreach ($request->answers as $answer) {
            $question = Question::find($answer['question_id']);
            if (!$question) {
                return response()->json(['message' => "Question not found: {$answer['question_id']}"], 404);
            }

            if ($question->type === 'single' && count($answer['selected_options']) !== 1) {
                return response()->json(['message' => 'Single choice questions must have exactly one answer'], 400);
            }

            if ($question->type === 'true_false' && count($answer['selected_options']) !== 1) {
                return response()->json(['message' => 'True/False questions must have exactly one answer'], 400);
            }
        }

        // Save answers
        $participation->answers = $request->answers;
        $participation->status = 'in_progress';
        $participation->save();

        return response()->json(['message' => 'Answers saved successfully'], 200);
    }


    public function submit($id)
    {
        $participation = Participation::with('contest.questions')->findOrFail($id);

        $score = 0;
        foreach ($participation->answers as $ans) {
            $question = $participation->contest->questions->firstWhere('id', $ans['question_id']);
            if ($question && $this->isCorrect($question, $ans['selected_options'])) {
                $score += $question->marks;
            }
        }

        $participation->update(['score' => $score, 'status' => 'submitted', 'submitted_at' => now()]);
        $this->updateLeaderboardAndAwardPrize($participation->contest_id);
        return response()->json(['message' => 'Participation submitted', 'score' => $score]);
    }

    private function isCorrect($question, $selected)
    {
        return collect($selected)->sort()->values()->toArray() === collect($question->correct_answers)->sort()->values()->toArray();
    }

    private function updateLeaderboardAndAwardPrize($contestId)
    {
        //  Find top participant based on score and submission time
        $topParticipant = Participation::where('contest_id', $contestId)
            ->where('status', 'submitted')
            ->orderByDesc('score')
            ->orderBy('submitted_at') // earliest submission breaks tie
            ->first();

        if (!$topParticipant) {
            return; // No valid participants yet
        }

        //  Fetch contest details
        $contest = Contest::find($contestId);

        if (!$contest) {
            return;
        }

        //  Create or update a prize record
        Prize::updateOrCreate(
            ['contest_id' => $contestId], // unique per contest
            [
                'user_id' => $topParticipant->user_id,
                'details' => $contest->prize_details,
                'awarded_at' => now(),
            ]
        );

        // log or event trigger for visibility
        \Log::info("Prize awarded for contest {$contest->name} to user {$topParticipant->user_id}");
    }

}
