<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Participation;
use App\Models\Contest;

class LeaderboardController extends Controller
{
    /**
     * Display top participants for a contest (with rank, score, and user info)
     */
    public function show($contestId)
    {
        $contest = Contest::find($contestId);

        if (!$contest) {
            return response()->json(['message' => 'Contest not found'], 404);
        }

        $participants = Participation::where('contest_id', $contestId)
            ->where('status', 'submitted')
            ->orderByDesc('score')
            ->orderBy('submitted_at') // tie-breaker: earlier submission = higher rank
            ->take(10)
            ->with('user:id,name')
            ->get(['id', 'user_id', 'contest_id', 'score', 'submitted_at']);

        if ($participants->isEmpty()) {
            return response()->json(['message' => 'No participants found'], 404);
        }

        // add rank number
        $ranked = $participants->values()->map(function ($item, $index) {
            return [
                'rank' => $index + 1,
                'user_name' => $item->user->name,
                'score' => $item->score,
                'submitted_at' => $item->submitted_at,
            ];
        });

        return response()->json([
            'contest_id' => $contestId,
            'contest_name' => $contest->name,
            'leaderboard' => $ranked
        ]);
    }


    public function showLeaderboardView(Request $request, $contestId)
    {
        $contest = Contest::findOrFail($contestId);

        $participants = Participation::where('contest_id', $contestId)
            ->where('status', 'submitted')
            ->orderByDesc('score')
            ->orderBy('submitted_at')
            ->with('user:id,name')
            ->paginate(10); // Laravel pagination

        return view('leaderboard', [
            'contest' => $contest,
            'participants' => $participants
        ]);
    }

}

