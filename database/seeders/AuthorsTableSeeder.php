<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Author;
use App\Models\AuthorDetail;

class AuthorsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // ファクトリによって 5 件の著者情報を作成
        // Author::factory(5)->create();
        // ファクトリによって 5 件の著者詳細情報を作成
        // 合わせて著者情報も 5 件作成される
        AuthorDetail::factory(5)->create();
    }
}
