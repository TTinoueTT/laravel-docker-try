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

    /** @test */
    public function バリデーション(): void
    {
        // 作成者で認証
        $this->actingAs($this->admin, 'admin');

        $url = route('admin.book.update', $this->book);

        // リダイレクト先の確認
        $this->from(route('admin.book.edit', $this->book))
            ->put($url, ['category_id' => ''])
            ->assertRedirect(route('admin.book.edit', $this->book));

        // 以降バリデーションとメッセージの確認
        // カテゴリIDが空
        $this->put($url, ['category_id' => ''])
            ->assertInvalid(['category_id' => 'カテゴリ は必須']);

        // カテゴリIDが0(カテゴリテーブルに存在しない)
        $this->put($url, ['category_id' => '0'])
            ->assertInvalid(['category_id' => '正しい カテゴリ']);

        // カテゴリ ID が3つ目のカテゴリのID(正常)
        $this->put($url, ['category_id' => $this->categories[2]->id])
            ->assertValid('category_id');

        // タイトルが空
        $this->put($url, ['title' => ''])
            ->assertInvalid(['title' => 'タイトル は必須入力']);

        // タイトルが1文字(正常)
        $this->put($url, ['title' => 'a'])->assertValid('title');

        // タイトルが100文字(正常)
        // str_repeat('a', 100) は 'a' を 100桁並べた文字列を返す
        $this->put($url, ['title' => str_repeat('a', 100)])
            ->assertValid('title');

        // タイトルが 101 文字
        $this->put($url, ['title' => str_repeat('a', 101)])
            ->assertInvalid(['title' => 'タイトル は 100 文字以内']);

        // 価格が数値ではない
        $this->put($url, ['price' => 'a'])
            ->assertInvalid(['price' => '価格 は数値']);

        // 価格が 0
        $this->put($url, ['price' => '0'])
            ->assertInvalid(['price' => '価格 は 1 以上']);

        // 価格が 1 (正常)
        $this->put($url, ['price' => '1'])->assertValid('price');

        // 価格が 999999 (正常)
        $this->put($url, ['price' => '999999'])->assertValid('price');

        // 価格が 1000000
        $this->put($url, ['price' => '1000000'])
            ->assertInvalid(['price' => '価格 は 999999 以下']);

        // 著者IDは複数なので配列形式で渡す
        // 著者IDが空
        $this->put($url, ['author_ids' => []])
            ->assertInvalid(['author_ids' => '著者 は必須入力']);

        // 著者 ID が 0 (著者テーブルに存在しない)
        $this->put($url, ['author_ids' => ['0']])
            ->assertInvalid(['author_ids.0' => '正しい 著者']);

        // 著者ID が 3つ目の著者のID(正常)
        $this->put($url, ['author_ids' => [$this->authors[2]->id]])
            ->assertValid('author_ids.0');
    }

    /** @test */
    public function 更新(): void
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

        // 書籍の作成者で認証
        $this->actingAs($this->admin, 'admin');

        // 更新後、書籍一覧へリダイレクトする
        $this->put($url, $param)
            ->assertRedirect(route('admin.book.index'));

        // 書籍テーブルがパラメータ通りに更新される
        $updateBook = [
            'id' => $this->book->id,
            'category_id' => $param['category_id'],
            'title' => $param['title'],
            'price' => $param['price'],
        ];
        $this->assertDatabaseHas('books', $updateBook);

        // 書籍と著者の関連付けが削除され、新しくパラメータ通りに登録される
        foreach ($this->authors as $author) {
            $authorBook = [
                'book_id' => $this->book->id,
                'author_id' => $author->id,
            ];

            // パラメータで指定された著者IDか否かを判定
            if (in_array($author->id, $param['author_ids'])) {
                // 指定の著者書籍情報が登録される
                $this->assertDatabaseHas('author_book', $authorBook);
            } else {
                // 指定されていない著者書籍情報が削除される
                $this->assertDatabaseMissing('author_book', $authorBook);
            }
        }

        // 完了メッセージが表示される
        $this->get(route('admin.book.index'))
            ->assertSee($param['title'] . 'を変更しました');
    }
}
