<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Participation;
use App\Models\Prize;

class PrizeController extends Controller
{
    /**
     * Get the authenticated user's contest participation history and prizes.
     */
    public function userHistory(Request $request)
    {
        $user = $request->user();

        // Fetch participation history
        $participations = Participation::with('contest:id,name,description,prize_details,start_at,end_at')
            ->where('user_id', $user->id)
            ->orderByDesc('submitted_at')
            ->paginate(10, ['id', 'contest_id', 'score', 'status', 'submitted_at']);

        // Fetch all prizes won
        $prizes = Prize::with('contest:id,name')
            ->where('user_id', $user->id)
            ->orderByDesc('awarded_at')
            ->get(['id', 'contest_id', 'details', 'awarded_at']);

        if ($participations->isEmpty() && $prizes->isEmpty()) {
            return response()->json([
                'message' => 'No participation or prize history found.',
            ], 404);
        }

        return response()->json([
            'user' => [
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
            ],
            'participation_history' => $participations,
            'prizes_won' => $prizes,
        ]);
    }

    public function contestWinner($contestId)
    {
        $prize = Prize::with(['user:id,name,email', 'contest:id,name,description,prize_details'])
            ->where('contest_id', $contestId)
            ->first();

        if (!$prize) {
            return response()->json([
                'message' => 'No winner found for this contest yet.'
            ], 404);
        }

        return response()->json([
            'contest' => [
                'id' => $prize->contest->id,
                'name' => $prize->contest->name,
                'description' => $prize->contest->description,
                'prize_details' => $prize->contest->prize_details,
            ],
            'winner' => [
                'id' => $prize->user->id,
                'name' => $prize->user->name,
                'email' => $prize->user->email,
            ],
            'awarded_at' => $prize->awarded_at,
        ]);
    }

    public function allContestWinners()
    {
        $prizes = Prize::with(['user:id,name,email', 'contest:id,name,description,prize_details,end_at'])
            ->orderByDesc('awarded_at')
            ->paginate(10, ['id', 'user_id', 'contest_id', 'details', 'awarded_at']);

        if ($prizes->isEmpty()) {
            return response()->json([
                'message' => 'No prizes have been awarded yet.'
            ], 404);
        }

        $data = $prizes->map(function ($prize) {
            return [
                'contest' => [
                    'id' => $prize->contest->id,
                    'name' => $prize->contest->name,
                    'description' => $prize->contest->description,
                    'end_at' => $prize->contest->end_at,
                    'prize_details' => $prize->contest->prize_details,
                ],
                'winner' => [
                    'id' => $prize->user->id,
                    'name' => $prize->user->name,
                    'email' => $prize->user->email,
                ],
                'awarded_at' => $prize->awarded_at,
            ];
        });

        return response()->json([
            'total_awarded' => $prizes->total(),
            'current_page' => $prizes->currentPage(),
            'data' => $data,
        ]);
    }


}

