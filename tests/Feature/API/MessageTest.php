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
}
