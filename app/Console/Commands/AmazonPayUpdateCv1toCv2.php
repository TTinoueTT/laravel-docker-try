<?php

namespace App\Console\Commands;

use Illuminate\Support\Carbon;
use Illuminate\Console\Command;
use App\Contexts\AmazonPayUpdateComponent;

class AmazonPayUpdateCv1ToCv2 extends Command
{
    /**
     * The name and signature of the console command.
     * size 更新対象のレコード数を表示
     * --id billing_agreements テーブルの id を指定して
     * --start 更新対象期間の開始日 更新対象のレコードの UPDATED_AT( 例. --start="2024-03-01\ 00:00:00")、デフォルトは今月初めの日時
     * --end 更新対象期間の終了日 更新対象のレコードの UPDATED_AT( 例. --end="2024-03-01\ 00:00:00")、デフォルトは今月末の日時
     * @var string
     */
    protected $signature = 'amazon-pay:update-cv1-to-cv2 {size?} {--id=*} {--start=} {--end=}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'AmazonPay dump the billing_agreement table replace to the subscription table';

    private $amazonPayUpdateComponent;

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct(AmazonPayUpdateComponent $amazonPayUpdateComponent)
    {
        parent::__construct();
        $this->amazonPayUpdateComponent = $amazonPayUpdateComponent;
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle(): void
    {
        $this->info('amazon-pay:update-cv1-to-cv2');

        $now = Carbon::now();

        $startOfMonthStr = $now->firstOfMonth()->startOfDay()->toDateTimeString(); // 今月初めの日時を取得
        $endOfMonthStr = $now->lastOfMonth()->endOfDay()->toDateTimeString(); // 今月末の日時を取得
        // $startOfMonthStr = $now->copy()->subMonth()->firstOfMonth()->startOfDay()->toDateTimeString(); // 先月初めの日時を取得
        // $endOfMonthStr = $now->copy()->subMonth()->lastOfMonth()->endOfDay()->toDateTimeString(); // 先月末の日時を取得

        $startOfMonthStr = $this->option('start') ? $this->option('start') : $startOfMonthStr;
        $endOfMonthStr = $this->option('end') ? $this->option('end') : $endOfMonthStr;


        if ($this->argument('size')) {
            // size オプションの処理
            $sizeInfo = $this->amazonPayUpdateComponent->sizeInfo($startOfMonthStr, $endOfMonthStr);
            $this->info("更新対象アカウント数: {$sizeInfo['size']}");
            $this->info("更新対象範囲開始日: {$sizeInfo['startOfMonthStr']}");
            $this->info("更新対象範囲終了日: {$sizeInfo['endOfMonthStr']}");
        } else {
            // update の処理
            $idList = $this->option('id');
            $this->amazonPayUpdateComponent->cv1_to_cv2($startOfMonthStr, $endOfMonthStr, $idList);
        }
    }
}
