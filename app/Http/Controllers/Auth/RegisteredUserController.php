<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Providers\RouteServiceProvider;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;
use Illuminate\View\View;

class RegisteredUserController extends Controller
{
    /**
     * Display the registration view.
     */
    public function create(): View
    {
        return view('auth.register');
    }

    /**
     * Handle an incoming registration request.
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    public function store(Request $request): RedirectResponse
    {
        /*
         * 1. バリデーション
         * Request を独自に定義しない場合は、validate メソッドにルールを渡すことで、
         * バリデーションを実行できる
         */
        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'lowercase', 'email', 'max:255', 'unique:' . User::class],
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
        ]);

        /*
         * 2. ユーザ情報をデータベースへ登録
         * password はハッシュ化した上で登録している
         */
        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
        ]);

        /*
         * 3. ユーザ登録イベントの発火
         * 確認メールを送信する設定にしていると、
         * 登録イベントに反応して、確認メールが送信される
         * ただし、デフォルトの設定では送信されない
         */
        event(new Registered($user));

        /*
         * 4. ユーザ情報を認証済みとして保持する
         * 引数に渡されたユーザ情報が認証済みとしてセッションに格納される
         */
        Auth::login($user);

        /*
         * 5. ホーム画面(dashboard)にリダイレクト
         * app/Providers/RouteServiceProvider::HOME には '/dashboard' が定義されている
         */
        return redirect(RouteServiceProvider::HOME);
    }
}
