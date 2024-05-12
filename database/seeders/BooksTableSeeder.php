<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Category;
use App\Models\Book;

class BooksTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Book::factory(3)->create();
        $categories = [
            // ファクトリで生成されるタイトルをを上書きする
            Category::factory()->create(['title' => 'programming']),
            Category::factory()->create(['title' => 'design']),
            Category::factory()->create(['title' => 'management']),
        ];

        foreach ($categories as $category) {
            // カテゴリ 1 件につき、2 件の書籍を登録する
            // ファクトリで生成される カテゴリID を上書きする
            Book::factory(2)->create(['category_id' => $category->id]);
        }
    }
}
