<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class AIController extends Controller
{
   public function chat(Request $request)
{
    try {

        $message = $request->message;

        $response = Http::withHeaders([
            'Authorization' => 'Bearer ' . env('AI_API_KEY'),
            'Content-Type' => 'application/json',
        ])->post(env('AI_API_URL'), [
            "model" => "gpt-4o-mini",
            "messages" => [
                [
                    "role" => "user",
                    "content" => $message
                ]
            ],
            "temperature" => 0.7
        ]);

        $data = $response->json();

        // 🔥 DEBUG SAFE OUTPUT
        if (!$response->successful()) {
            return response()->json([
                'reply' => 'OpenAI Error: ' . json_encode($data)
            ]);
        }

        return response()->json([
            'reply' =>
                $data['choices'][0]['message']['content']
                ?? 'Empty response from OpenAI'
        ]);

    } catch (\Exception $e) {

        return response()->json([
            'reply' => 'Server Error: ' . $e->getMessage()
        ]);
    }
}
}