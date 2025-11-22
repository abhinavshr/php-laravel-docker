<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use App\Models\GuestPrompt;

class ChatController extends Controller
{
    private function callGemini($message)
    {
        $apiKey = env('GEMINI_API_KEY');
        $model = 'models/gemini-flash-latest';
        $url = "https://generativelanguage.googleapis.com/v1beta/{$model}:generateContent?key={$apiKey}";

        $response = Http::post($url, [
            "contents" => [
                [
                    "parts" => [
                        ["text" => $message]
                    ]
                ]
            ]
        ]);

        if ($response->failed()) {
            return response()->json([
                "error" => "Gemini API error",
                "details" => $response->json()
            ], 500);
        }

        $reply = $response->json()['candidates'][0]['content']['parts'][0]['text'] ?? 'No reply';

        return response()->json([
            "reply" => $reply
        ]);
    }

    public function chat(Request $request)
    {
        $request->validate(["message" => "required|string"]);

        return $this->callGemini($request->message);
    }

    public function guestChat(Request $request)
    {
        $ip = $request->ip();

        $guest = GuestPrompt::where('ip', $ip)->first();

        if (!$guest) {
            $guest = GuestPrompt::create([
                'ip' => $ip,
                'count' => 0,
                'last_prompt_at' => now()
            ]);
        }

        if ($guest->last_prompt_at && $guest->last_prompt_at->diffInHours(now()) >= 24) {
            $guest->count = 0;
        }

        if ($guest->count >= 5) {
            return response()->json([
                "error" => "Daily limit reached",
                "message" => "You have used all 5 free prompts. Login to continue."
            ], 403);
        }

        $guest->count += 1;
        $guest->last_prompt_at = now();
        $guest->save();

        return $this->callGemini($request->message);
    }
}
