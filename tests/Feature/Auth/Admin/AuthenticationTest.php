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
}
