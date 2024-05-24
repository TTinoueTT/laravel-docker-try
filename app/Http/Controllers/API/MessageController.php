<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Database\Eloquent\Collection;
use App\Models\Message;


class MessageController extends Controller
{
    public function index(): Collection
    {
        $messages = Message::all();

        return $messages;
    }

    public function show(Message $message): Message
    {
        return $message;
    }

    public function store(Request $request): Message
    {
        $message = new Message();
        $message->body = $request->body;
        $message->save();

        return $message;
    }

    public function destroy(Message $message): Response
    {
        $message->delete();

        // HTTPステータスコード204(コンテンツなし)を返す
        return response()->noContent();
    }
}
