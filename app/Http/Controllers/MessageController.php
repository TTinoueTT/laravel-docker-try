<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;
use App\Models\Message;
use Illuminate\Support\Facades\DB;

class MessageController extends Controller
{
    public function index(): View
    {
        $messages = Message::all();
        return view('message/index', ['messages' => $messages]);
    }

    public function store(Request $request): RedirectResponse
    {
        $message = new Message();
        $message->body = $request->body;
        $message->save();

        return redirect('/messages');
    }

    public function destroy(Message $message): RedirectResponse
    // public function destroy(string $id): RedirectResponse
    {
        $message->delete();
        // 削除処理
        // DB::delete('delete from messages where id = ' . $id);

        // プレースホルダを使用して削除
        // DB::delete('delete from messages where id = ?', [$id]);

        return redirect('/messages');
    }
}
