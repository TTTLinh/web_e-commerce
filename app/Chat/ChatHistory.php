<?php
namespace App\Chat;

use Illuminate\Support\Facades\Session;

class ChatHistory
{
    public function addMessage($user, $message)
    {
        $chatHistory = Session::get('chat_history', []);

        $chatHistory[] = [
            'user' => $user,
            'message' => $message,
        ];

        Session::put('chat_history', $chatHistory);
    }

    public function getChatHistory()
    {
        return Session::get('chat_history', []);
    }

    public function clearChatHistory()
    {
        Session::forget('chat_history');
    }
}