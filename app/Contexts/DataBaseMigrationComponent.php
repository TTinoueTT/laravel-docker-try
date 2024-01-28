<?php

namespace App\Contexts;

use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;

class DataBaseMigrationComponent
{
    public static function migrate_exec(): void
    {
        Log::info("start database migrate execution");
        // DB に接続
        $array = DB::connection('mysql_old')->select('select * from users_data');
        // DB::connection('mysql_old')->select('select * from users');
        // users テーブルの一覧を取得して、
        Log::info($array);
    }
}
