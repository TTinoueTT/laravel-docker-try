<?php

namespace Tests\Feature\Auth\Admin;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use App\Models\Admin;

class AuthenticationTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function ログイン画面の表示(): void
    {
        $this->get(route('admin.create'))
            ->assertOk();
    }

    /** @test */
    public function ログイン成功(): void
    {
        // 1. ログイン用ユーザ作成
        $admin = Admin::factory()->create([
            'login_id' => 'hoge',
            'password' => \Hash::make('hogehoge'),
        ]);

        // 2. ログイン成功すると書籍一覧にリダイレクトする
        $this->post(route('admin.store'), [
            'login_id' => 'hoge',
            'password' => 'hogehoge',
        ])->assertRedirect(route('admin.book.index'));

        // 3. 認証されている
        $this->assertAuthenticatedAs($admin, 'admin');
    }

    /** @test */
    public function ログイン失敗(): void
    {
        // 事前情報としてログイン用ユーザ作成
        $admin = Admin::factory()->create([
            'login_id' => 'hoge',
            'password' => \Hash::make('hogehoge'),
        ]);

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
}
