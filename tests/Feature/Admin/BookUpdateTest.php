<?php

namespace Tests\Feature\Admin;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use App\Models\Admin;
use App\Models\Category;
use App\Models\Book;
use App\Models\Author;

class BookUpdateTest extends TestCase
{
    use RefreshDatabase;

    private $admin;
    private $categories;
    private $book;
    private $authors;


    public function setUp(): void
    {
        parent::setUp();

        // ログイン用ユーザ作成
        $this->admin = Admin::factory()->create([
            'login_id' => 'hoge',
            'password' => \Hash::make('hogehoge'),
        ]);

        // カテゴリ3件作成
        $this->categories = Category::factory(3)->create();

        // 更新対象の書籍１件作成
        $this->book = Book::factory()->create([
            'title' => 'Laravel Book',
            'admin_id' => $this->admin->id,
            'category_id' => $this->categories[1]->id,
        ]);

        // 著者4件作成
        $this->authors = Author::factory(4)->create();

        // 著者4件中2件を書籍に関連付け
        $this->book->authors()->attach([
            $this->authors[0]->id,
            $this->authors[2]->id,
        ]);
    }

    /** @test */
    public function 画面のアクセス制御(): void
    {
        $url = route('admin.book.edit', $this->book);

        // 未認証の場合、更新画面にアクセス不可
        $this->get($url)
            ->assertRedirect(route('admin.create'));

        // 書籍の作成者とは異なるユーザで認証
        $other = Admin::factory()->create();
        $this->actingAs($other, 'admin');

        // 書籍の作成者ではない場合、更新画面にアクセス不可
        $this->get($url)
            ->assertForbidden();  // 403

        // 作成者で認証
        $this->actingAs($this->admin, 'admin');

        // 書籍の作成者の場合、更新画面にアクセス不可
        $this->get($url)
            ->assertOk();
    }

    /** @test */
    public function 更新処理のアクセス制御(): void
    {
        $url = route('admin.book.update', $this->book);

        // 入力データ
        $param = [
            'category_id' => $this->categories[0]->id,
            'title' => 'New Laravel Book',
            'price' => '10000',
            'author_ids' => [
                $this->authors[1]->id,
                $this->authors[2]->id,
            ],
        ];

        // 未認証の場合、更新不可
        $this->put($url, $param)
            ->assertRedirect(route('admin.create'));

        // 書籍の作成者とは異なるユーザで認証
        $other = Admin::factory()->create();
        $this->actingAs($other, 'admin');

        // 書籍の作成者でない場合、更新不可(403)
        $this->put($url, $param)
            ->assertForbidden();

        // 書籍が更新されていないこと
        $this->assertSame('Laravel Book', $this->book->fresh()->title);

        // 「書籍の作成者の場合、更新可」のテストは更新テストで行う
    }
}
