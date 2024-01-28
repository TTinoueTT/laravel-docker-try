<?php

namespace App\Contexts;

use App\Models\OldUser;
use App\Models\OldUserData;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Eloquent\Collection;

class DataBaseMigrationComponent
{
    public static function migrate_exec(): void
    {
        Log::info("start database migrate execution");

        // OldUser::chunk(200, function (Collection $users) {
        //     foreach ($users as $user) {
        //         // ...
        $user = OldUser::find(1);

        # profile の取得と、target_profiles の取得
        # histories の取得
        //     }
        // });

        // DB に接続
        // $array = OldUser::all();
        // $array = OldUserData::all();
        $userData = OldUserData::find(3);
        // $array = DB::connection('mysql_old')->select('select * from users_data');
        // DB::connection('mysql_old')->select('select * from users');
        # ① users テーブルの一覧を取得
        # ② users に関与するテーブルごとに新規DBにインサート処理を行う
        # たとえば、
        Log::info($userData->user_id);
    }
}
