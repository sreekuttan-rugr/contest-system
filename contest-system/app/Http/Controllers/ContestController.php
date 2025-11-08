<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Contest;
use Illuminate\Validation\ValidationException;


class ContestController extends Controller
{
    public function index(Request $request)
    {
        $user = $request->user();
        $query = Contest::query();

        if (!$user || $user->role === 'guest') {
            $query->where('access_level', 'normal');
        } elseif ($user->role === 'user') {
            $query->where('access_level', 'normal');
        } // VIP/Admin get all

        return response()->json($query->get());
    }

    public function show($id)
    {
        $contest = Contest::with('questions')->findOrFail($id);
        return response()->json($contest);
    }

    public function store(Request $request)
    {
        try {
            $validated = $request->validate([
                'name' => 'required|string|max:255',
                'description' => 'nullable|string|max:1000',
                'access_level' => 'required|in:normal,vip',
                'start_at' => 'required|date|after_or_equal:now',
                'end_at' => 'required|date|after:start_at',
                'status'=> 'optional|in:draft,live'

            ]);
        } catch (ValidationException $e) {
            return response()->json([
                'message' => 'Validation failed',
                'errors' => $e->errors()
            ], 422);
        }

        $contest = Contest::create($validated + ['created_by' => auth()->id()]);

        return response()->json($contest, 201);
    }

}
