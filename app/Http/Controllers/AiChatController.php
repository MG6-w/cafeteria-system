<?php

namespace App\Http\Controllers;

use App\Services\AiChatbotService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AiChatController extends Controller
{
    protected AiChatbotService $aiService;

    public function __construct(AiChatbotService $aiService)
    {
        $this->aiService = $aiService;
    }

    public function index()
    {
        $user = Auth::user();
        return view('ai.index', compact('user'));
    }

    public function chat(Request $request)
    {
        $request->validate([
            'message' => 'required|string|max:1000',
        ]);

        $user = Auth::user();
        $response = $this->aiService->handleUserMessage($user, $request->message);

        return response()->json($response);
    }
}
