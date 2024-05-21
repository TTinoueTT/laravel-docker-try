<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class DatabaseServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        // データベースのクエリを監視
        DB::listen(function ($query) {
            Log::info('query', [
                'sql' => $query->sql,           // 実行されたSQL
                'bindings' => $query->bindings, // バインドされたパラメータ
                'time' => $query->time,         // クエリの実行時間
            ]);
        });
    }
}
