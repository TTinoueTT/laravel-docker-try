<?php

namespace Tests\Feature\Auth\Admin;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use App\Models\Admin;

class AuthenticationTest extends TestCase
{
    use RefreshDatabase;

    private $admin;

    public function setUp(): void
    {
        // 親のsetUpメソッド呼び出し
        parent::setUp();

        // ログインテスト用ユーザ作成
        $this->admin = Admin::factory()->create([
            'login_id' => 'hoge',
            'password' => \Hash::make('hogehoge'),
        ]);
    }

    /** @test */
    public function ログイン画面の表示(): void
    {
        $this->get(route('admin.create'))
            ->assertOk();
    }

    /** @test */
    public function ログイン成功(): void
    {
        // // 1. ログイン用ユーザ作成
        // $admin = Admin::factory()->create([
        //     'login_id' => 'hoge',
        //     'password' => \Hash::make('hogehoge'),
        // ]);

        // 2. ログイン成功すると書籍一覧にリダイレクトする
        $this->post(route('admin.store'), [
            'login_id' => 'hoge',
            'password' => 'hogehoge',
        ])->assertRedirect(route('admin.book.index'));

        // 3. 認証されている
        $this->assertAuthenticatedAs($this->admin, 'admin');
    }

    /** @test */
    public function ログイン失敗(): void
    {
        // // 事前情報としてログイン用ユーザ作成
        // $admin = Admin::factory()->create([
        //     'login_id' => 'hoge',
        //     'password' => \Hash::make('hogehoge'),
        // ]);

        // ID が一致しない場合
        $this->from(route('admin.store'))
            ->post(route('admin.store'), [
                'login_id' => 'fuga',
                'password' => 'hogehoge',
            ])
            ->assertRedirect(route('admin.create'))
            ->assertInvalid(['login_id' => 'These credentials do not match']);

        // パスワードが一致しない場合
        $this->from(route('admin.store'))
            ->post(route('admin.store'), [
                'login_id' => 'hoge',
                'password' => 'fugafuga',
            ])
            ->assertRedirect(route('admin.create'))
            ->assertInvalid(['login_id' => 'These credentials do not']);

        // 認証されていない
        $this->assertGuest('admin');
    }

    /** @test */
    public function バリデーション(): void
    {
        $url = route('admin.store');

        // リダイレクト
        $this->from(route('admin.store'))
            ->post(route('admin.store'), [
                'login_id' => '',
            ])->assertRedirect(route('admin.create'));

        // ID未入力
        $this->post($url, [
            'login_id' => '',
        ])->assertInvalid(['login_id' => 'login id は必須']);

        // ID入力
        $this->post($url, [
            'login_id' => 'a',
        ])->assertValid('login_id');

        // パスワード未入力
        $this->post($url, [
            'password' => '',
        ])->assertInvalid(['password' => 'password は必須']);

        // パスワード入力
        $this->post($url, [
            'password' => 'a',
        ])->assertValid('password');
    }
}
