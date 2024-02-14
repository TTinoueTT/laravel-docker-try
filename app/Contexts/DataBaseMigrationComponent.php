<?php

namespace App\Contexts;

use App\Models\Old\OldUser;
use App\Models\Next\NextUser;
use App\Models\Old\OldProfile;
use App\Models\Old\OldUserData;
use App\Services\BookmarkService;
use App\Services\HistoryService;
use App\Services\ProfileService;
use App\Services\UserAnalysisService;
use App\Services\UserDataService;
use App\Services\UserService;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Eloquent\Collection;
use RuntimeException;

class DataBaseMigrationComponent
{
    private $userService;
    private $profileService;
    private $historyService;
    private $bookmarkService;
    private $userDataService;
    private $userAnalysisService;


    public function __construct(
        ProfileService $profileService,
        UserService $userService,
        HistoryService $historyService,
        BookmarkService $bookmarkService,
        UserDataService $userDataService,
        UserAnalysisService $userAnalysisService
    ) {
        $this->userService = $userService;
        $this->profileService = $profileService;
        $this->historyService = $historyService;
        $this->bookmarkService = $bookmarkService;
        $this->userDataService = $userDataService;
        $this->userAnalysisService = $userAnalysisService;
    }

    public function migrate_exec(): void
    {

        $k = "\/\\\\";
        $l = "\\\\\\\\";
        Log::info("\/\/\/\/\/\/\/\/\/\/\/\/\/\/\/\/\/\/\/\/\/\/\/\/\/\/\/\/\/\/\/\/\/\/\/\/\/\/\/\/\/\/\/\/\/\/\/\/\/\/\/\/");
        Log::info("//\/\/\/\/\/\/\/\/\/\/\/\/\/\/\/\/\/\/\/\/\/\/\/\/\/\/\/\/\/\/\/\/\/\/\/\/\/\/\/\/\/\/\/\/\/\/\/\/\/{$k}");
        Log::info("{$l}  _____    _____        ___    ___   ___    ____    _____      ___    _______   ____    _____   ////");
        Log::info("//// (  __ \  (  __ \  ___ (   \  /   ) (   )  / ___)  (  __ \    / _ \  (__   __) / __ \  (  __ \  {$l}");
        Log::info("{$l} | |__) ) )  __ < (___) ) \ \/ / (   ) (  ( (_(  )  ) _  /   / ___ \    ) (   ( (__) )  ) _  /  ////");
        Log::info("//// (_____/  (_____/      (__)\__/(__) (___)  \____/  (__)(__) (__) (__)  (___)   \____/  (__)(__) {$l}");
        Log::info("{$l}                                                                                                ////");
        Log::info("//\/\/\/\/\/\/\/\/\/\/\/\/\/\/\/\/\/\/\/\/\/\/\/\/\/\/\/\/\/\/\/\/\/\/\/\/\/\/\/\/\/\/\/\/\/\/\/\/\/{$k}");
        Log::info("\/\/\/\/\/\/\/\/\/\/\/\/\/\/\/\/\/\/\/\/\/\/\/\/\/\/\/\/\/\/\/\/\/\/\/\/\/\/\/\/\/\/\/\/\/\/\/\/\/\/\/\/");
        Log::info("START DATABASE MIGRATE EXECUTION");

        $repeatTime = 50;
        $counter = 0;

        DB::connection('mysql_old')->beginTransaction();
        DB::connection('mysql_new')->beginTransaction();
        DB::connection('mysql_new_payment')->beginTransaction();

        try {
            OldUser::orderBy('id', 'asc')->chunk($repeatTime, function (Collection $oldUsers) use ($repeatTime, $counter) {
                // OldUser::orderBy('id', 'desc')->chunk($repeatTime, function (Collection $oldUsers) use ($repeatTime, $counter) {
                // 処理回数を追跡するカウンタ
                foreach ($oldUsers as $oldUser) {

                    // TODO: 練習用に修正
                    /*
                    * user_id
                    * => 3 AmazonPay
                    * => 5 Docomo
                    * => 11 Au
                    * => 190 Softbank
                    * => 22, 21 Rakuten
                    */
                    // if ($oldUser->id != 65) {
                    //     continue;
                    // }

                    # users レコードの移行(決済継続データの移行も)
                    Log::info("*************************************************************");
                    Log::info("Start migrate user ((( id: {$oldUser->id} )))");
                    Log::info("↓↓↓↓↓↓↓↓↓↓↓↓↓↓↓↓↓↓↓↓↓↓↓↓↓↓↓↓↓↓↓↓↓↓↓↓↓↓↓↓↓↓↓↓↓↓↓↓↓↓↓↓↓↓↓↓↓↓↓↓↓");

                    $nextUser = $this->userService->migrateOldToNew($oldUser);
                    if ($nextUser == null) {
                        Log::info("↑↑↑↑↑↑↑↑↑↑↑↑↑↑↑↑↑↑↑↑↑↑↑↑↑↑↑↑↑↑↑↑↑↑↑↑↑↑↑↑↑↑↑↑↑↑↑↑↑↑↑↑↑↑↑↑↑↑↑↑↑");
                        Log::info("New user is null, old user ((( id :{$oldUser->id} ))) has invalid parameter");
                        Log::info("*************************************************************");
                        continue;
                    }

                    # profile の移行(旧profile情報の重複も考慮)
                    $migrateProfileIdMap = $this->profileService->migrateOldToNewWithNew($oldUser, $nextUser);

                    # history の移行(決済注文レコードの移行も)
                    $this->historyService->migrateOldToNewWithNew($oldUser, $nextUser, $migrateProfileIdMap);

                    # bookmark の移行
                    $this->bookmarkService->migrateOldToNewWithNew($oldUser, $nextUser);

                    # usersData の移行
                    $this->userDataService->migrateOldToNewWithNew($oldUser, $nextUser);

                    # userAnalysis の移行
                    $this->userAnalysisService->migrateOldToNewWithNew($oldUser, $nextUser);

                    Log::info("↑↑↑↑↑↑↑↑↑↑↑↑↑↑↑↑↑↑↑↑↑↑↑↑↑↑↑↑↑↑↑↑↑↑↑↑↑↑↑↑↑↑↑↑↑↑↑↑↑↑↑↑↑↑↑↑↑↑↑↑↑");
                    Log::info("old user ((( id :{$oldUser->id} ))) => migrate to new user ((( id: {$nextUser->id} )))");
                    Log::info("*************************************************************");

                    Log::info("======#{$counter}");
                    // chunk の処理を止めたい カウンタをインクリメント
                    // $counter++;
                }

                // 50回処理した後にchunkの処理を停止
                // if ($counter == $repeatTime) { // 1回のチャンク処理後に停止したい場合
                //     return false; // これにより、chunk処理が停止されます。
                // }
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
    }
}
