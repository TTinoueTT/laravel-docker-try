<?php

namespace App\Contexts;

use App\Models\Old\OldUser;
use App\Models\Old\OldProfile;
use App\Models\Old\OldUserData;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Eloquent\Collection;

class DataBaseMigrationComponent
{
    public static function migrate_exec(): void
    {
        $repeatTime = 50;
        $counter = 0;
        Log::info("start database migrate execution");

        OldUser::chunk($repeatTime, function (Collection $users) use ($repeatTime, $counter) {
            // 処理回数を追跡するカウンタ
            foreach ($users as $user) {
                // ...
                // $user = OldUser::find(1);
                Log::info("users id : #{$user->id}");

                $profiles = $user->profiles;
                // OldProfile::find($user->id)->each(function (OldProfile $oldProfile) use ($counter, $user) {

                // # profile の取得と、target_profiles の取得
                // # histories の取得
                // });
                foreach ($profiles as $profile) {
                    log::info("profile id : #{$profile->id}");
                }

                log::info("======#{$counter}");
                // 一旦chunk の処理を止めたい
                // カウンタをインクリメント
                $counter++;

                // 50回処理した後にchunkの処理を停止
                if ($counter == $repeatTime) { // 1回のチャンク処理後に停止したい場合
                    return false; // これにより、chunk処理が停止されます。
                }
            }
        });



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
