<?php

namespace App\Http\Controllers;

use App\Models\Question;
use App\Models\Contest;
use Illuminate\Http\Request;

class QuestionController extends Controller
{
    /**
     * List all questions for a given contest
     */
    public function index($contestId)
    {
        $contest = Contest::findOrFail($contestId);
        $questions = $contest->questions()->get();


        return response()->json([
            'contest' => $contest->name,
            'questions' => $questions
        ]);
    }

    /**
     * Store a new question (Admin only)
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'contest_id' => 'required|exists:contests,id',
            'type' => 'required|in:single,multi,true_false',
            'question_text' => 'required|string',
            'options' => 'required|array|min:2',
            'correct_answers' => 'required|array|min:1',
            'marks' => 'required|integer|min:1'
        ]);
        if($validated['type'] === 'single' || $validated['type'] === 'true_false') {
            if(count($validated['correct_answers']) !== 1) {
                return response()->json(['message' => 'Single choice and True/False questions must have exactly one correct answer.'], 422);
            }
        }
        if($validated['type'] === 'true_false') {
            $validOptions = ['A', 'B'];
            foreach ($validated['options'] as $key => $value) {
                if (!in_array($key, $validOptions)) {
                    return response()->json(['message' => 'True/False questions can only have options A and B.'], 422);
                }
            }
        }
        if($validated['type'] === 'multi') {
            if(count($validated['correct_answers']) < 1) {
                return response()->json(['message' => 'Multiple choice questions must have at least one correct answer.'], 422);
            }
        }


        $question = Question::create([
            'contest_id' => $validated['contest_id'],
            'type' => $validated['type'],
            'question_text' => $validated['question_text'],
            'options' => $validated['options'],
            'correct_answers' => $validated['correct_answers'],
            'marks' => $validated['marks']
        ]);

        return response()->json([
            'message' => 'Question created successfully',
            'data' => $question
        ], 201);
    }

    /**
     * Update question details
     */
    public function update(Request $request, $id)
    {
        $question = Question::findOrFail($id);

        $validated = $request->validate([
            'question_text' => 'sometimes|string',
            'options' => 'sometimes|array',
            'correct_answers' => 'sometimes|array',
            'marks' => 'sometimes|integer|min:1'
        ]);
        


        $question->update([
            'question_text' => $validated['question_text'] ?? $question->question_text,
            'options' => isset($validated['options']) ? $validated['options'] : $question->options,
            'correct_answers' => isset($validated['correct_answers']) ? $validated['correct_answers'] : $question->correct_answers,
            'marks' => $validated['marks'] ?? $question->marks,
        ]);


        return response()->json([
            'message' => 'Question updated successfully',
            'data' => $question
        ]);
    }

    /**
     * Delete question
     */
    public function destroy($id)
    {
        $question = Question::findOrFail($id);
        $question->delete();

        return response()->json(['message' => 'Question deleted successfully']);
    }


    /**
     * Create Questions in Bulk
     */

     public function storeBulk(Request $request)
    {
        $validated = $request->validate([
            'questions' => 'required|array|min:1',
            'questions.*.contest_id' => 'required|exists:contests,id',
            'questions.*.type' => 'required|in:single,multi,true_false',
            'questions.*.question_text' => 'required|string',
            'questions.*.options' => 'required|array|min:2',
            'questions.*.correct_answers' => 'required|array|min:1',
            'questions.*.marks' => 'required|integer|min:1',
        ]);

        $createdQuestions = [];

        foreach ($validated['questions'] as $q) {
            // Validation logic same as single store
            if (in_array($q['type'], ['single', 'true_false']) && count($q['correct_answers']) !== 1) {
                return response()->json(['message' => 'Single choice and True/False questions must have exactly one correct answer.'], 422);
            }

            if ($q['type'] === 'true_false') {
                $validOptions = ['A', 'B'];
                foreach ($q['options'] as $key => $value) {
                    if (!in_array($key, $validOptions)) {
                        return response()->json(['message' => 'True/False questions can only have options A and B.'], 422);
                    }
                }
            }

            // Create question
            $createdQuestions[] = Question::create([
                'contest_id' => $q['contest_id'],
                'type' => $q['type'],
                'question_text' => $q['question_text'],
                'options' => $q['options'],
                'correct_answers' => $q['correct_answers'],
                'marks' => $q['marks']
            ]);
        }

        return response()->json([
            'message' => count($createdQuestions) . ' questions created successfully',
            'data' => $createdQuestions
        ], 201);
    }


}
