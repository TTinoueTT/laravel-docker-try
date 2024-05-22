<?php

namespace Tests\Feature;

use App\Models\Message;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class MessageTest extends TestCase
{
    use RefreshDatabase;
    /**
     * A basic feature test example.
     */
    // public function test_example(): void
    // {
    //     $response = $this->get('/');

    //     $response->assertStatus(200);
    // }

    /**
     * @test
     */
    public function test_messages_index(): void
    {
        // 事前情報として、メッセージを作成
        Message::create(['body' => "Hello World"]);
        Message::create(['body' => "Hello Laravel"]);
        // メッセージ一覧にリクエストを送信し、200(OK)が返る
        $this->get('messages')
            ->assertOk()
            ->assertSeeInOrder([
                'Hello World',
                'Hello Laravel'
            ]);
    }
}
