<?php

namespace Tests\Feature\API;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use App\Models\Message;

class MessageTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function 一覧取得(): void
    {
        $message1 = Message::create(['body' => 'Hello']);
        $message2 = Message::create(['body' => 'Hi']);

        $this->getJson(route('api.message.index'))
            ->assertOk()
            ->assertJson([
                'data' => [
                    [
                        'type' => 'message',
                        'id' => $message1->id,
                        'body' => $message1->body,
                        'url' => url('/messages/' . $message1->id),
                    ],
                    [
                        'type' => 'message',
                        'id' => $message2->id,
                        'body' => $message2->body,
                        'url' => url('/messages/' . $message2->id),
                    ]
                ]
            ]);
    }

    /** @test */
    public function 一件取得(): void
    {
        $message = Message::create(['body' => 'Hello']);

        $this->getJson(route('api.message.show', $message))
            ->assertOk()
            ->assertJson([
                'data' => [
                    'type' => 'message',
                    'id' => $message->id,
                    'body' => $message->body,
                    'url' => url('/messages/' . $message->id),
                ]
            ]);
    }

    /** @test */
    public function 登録(): void
    {
        $message = ['body' => 'Bye']; // 登録用データ

        $this->postJson(route('api.message.store'), $message)
            ->assertStatus(201)
            ->assertJson($message);

        $this->assertDatabaseHas('messages', $message);
    }

    /** @test */
    public function 削除(): void
    {
        $message = Message::create(['body' => 'Good']);

        $this->deleteJson('api/messages/' . $message->id)
            ->assertStatus(204);

        $this->assertModelMissing($message);
    }
}
