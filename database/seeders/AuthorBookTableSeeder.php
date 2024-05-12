<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Book;
use App\Models\Author;

class AuthorBookTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $books = Book::all();
        $authors = Author::all();

        foreach ($books as $book) {
            $authorIds = $authors
                ->random(2) // 2 件著者をランダムに抽出
                ->pluck('id') // 著者モデルから ID のみを抽出する
                ->all();

            // 書籍にランダムに抜き出した 2 件の著者の ID 配列を関連づける
            $book->authors()->attach($authorIds);
        }
    }
}
