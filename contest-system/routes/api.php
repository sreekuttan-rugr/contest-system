<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\ContestController;
use App\Http\Controllers\ParticipationController;
use App\Http\Controllers\LeaderboardController;
use App\Http\Controllers\PrizeController;
use App\Http\Controllers\QuestionController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);

// Public
Route::get('/contests', [ContestController::class, 'index']);
Route::get('/contests/{id}/leaderboard', [LeaderboardController::class, 'show']);

// Protected routes
Route::middleware(['auth:sanctum'])->group(function () {
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::post('/contests/{id}/join', [ParticipationController::class, 'join']);
    Route::Get('/participations/{id})',[ParticipationController::class, 'showQuestions']);
    Route::post('/participations/{id}/save', [ParticipationController::class, 'saveAnswers']);
    Route::post('/participations/{id}/submit', [ParticipationController::class, 'submit']);
    Route::get('/user/history', [PrizeController::class, 'userHistory']);
    Route::get('/contest/{contestId}/winner', [PrizeController::class, 'contestWinner']);

});


// Admin-only
Route::middleware(['auth:sanctum', 'role:admin'])->group(function () {
    Route::post('/admin/contests', [ContestController::class, 'store']);
    Route::post('/admin/questions', [QuestionController::class, 'store']);
    Route::put('/admin/questions/{id}', [QuestionController::class, 'update']);
    Route::delete('/admin/questions/{id}', [QuestionController::class, 'destroy']);
    Route::get('/admin/contests/{id}/questions', [QuestionController::class, 'index']);
    Route::post('/admin/questions/bulk', [QuestionController::class, 'storeBulk']);
    Route::get('/contests/{id}', [ContestController::class, 'show']);
    Route::get('/admin/prizes', [PrizeController::class, 'allContestWinners']);

});
