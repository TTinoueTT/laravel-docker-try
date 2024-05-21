<?php

namespace App\Http\Controllers\Auth\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\Admin\LoginRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Illuminate\Support\Facades\Auth;

class AuthenticatedSessionController extends Controller
{
    // ログイン画面の表示
    public function create(): View
    {
        return view('auth/admin/login');
    }

    // 認証処理の実行
    public function store(LoginRequest $request): RedirectResponse
    {
        $request->authenticate();
        $request->session()->regenerate();
        return redirect(route('admin.book.index'));
    }

    // ログアウトの実行
    public function destroy(Request $request): RedirectResponse
    {
        /*
         * ログアウト時にガードの指定は不要
         * Auth ファサードを使うときに自動的に
         * ログイン時のガードを参照してくれる
         */
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect(route('admin.create'));
    }
}
