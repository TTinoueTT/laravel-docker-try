<?php

namespace App\Contexts;

use App\Models\Old\OldUser;
use App\Models\Next\NextUser;
use App\Models\Old\OldProfile;
use App\Models\Old\OldUserData;
use App\Services\ProfileService;
use App\Services\UserService;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Eloquent\Collection;
use RuntimeException;

class DataBaseMigrationComponent
{
    private $userService;
    private $profileServices;
    public function __construct(ProfileService $profileServices, UserService $userService)
    {
        // DI の実行
        $this->userService = $userService;
        $this->profileServices = $profileServices;
    }

    public function migrate_exec(): void
    {
        $repeatTime = 200;
        $counter = 0;
        Log::info("start database migrate execution");

        DB::connection('mysql_old')->beginTransaction();
        DB::connection('mysql_new')->beginTransaction();
        DB::connection('mysql_new_payment')->beginTransaction();

        try {
            OldUser::chunk($repeatTime, function (Collection $users) use ($repeatTime, $counter) {
                // 処理回数を追跡するカウンタ
                foreach ($users as $user) {

                    // TODO: 練習用に修正
                    /*
                    * user_id
                    * => 3 AmazonPay
                    * => 5 Docomo
                    * => 11 Au
                    * => 190 Softbank
                    * => 22 Rakuten
                    */
                    if ($user->id != 3) {
                        continue;
                    }

                    # users レコードの移行
                    Log::info("users id: {$user->id}");
                    $nextUser = $this->userService->migrateOldToNew($user);

                    # profile の移行
                    $this->profileServices->migrateOldToNewWithNew($user, $nextUser);

                    # history の移行

                    // new DB の user 読み込み
                    // $nextUser = NextUser::find(1);
                    // Log::info("users external_id: {$nextUser->external_id}");
                    // NextUser::where(NextUser::EXTERNAL_ID, $user->email)->first();

                    # targetProfile の取得

                    # histories の取得

                    #



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

            // すべての操作が成功したら、各トランザクションをコミット
            DB::connection('mysql_old')->commit();
            DB::connection('mysql_new')->commit();
            DB::connection('mysql_new_payment')->commit();
        } catch (\Exception $e) {
            // エラーが発生した場合は、全てのトランザクションをロールバック
            DB::connection('mysql_old')->rollBack();
            DB::connection('mysql_new')->rollBack();
            DB::connection('mysql_new_payment')->rollBack();
            //throw $th;
            // エラーメッセージと例外の詳細をログに記録
            Log::error('トランザクション失敗:', ['message' => $e->getMessage(), 'exception' => $e]);

            // 必要に応じてカスタム例外を投げる
            throw new RuntimeException("トランザクション中にエラーが発生しました。", 0, $e);
        }



        // DB に接続
        // $array = OldUser::all();
        // $array = OldUserData::all();
        // $userData = OldUserData::find(3);
        // $array = DB::connection('mysql_old')->select('select * from users_data');
        // DB::connection('mysql_old')->select('select * from users');
        # ① users テーブルの一覧を取得
        # ② users に関与するテーブルごとに新規DBにインサート処理を行う
        # たとえば、
        // Log::info($userData->user_id);
    }
}
