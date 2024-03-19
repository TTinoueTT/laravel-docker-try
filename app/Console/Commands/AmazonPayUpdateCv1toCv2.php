<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Contexts\AmazonPayUpdateComponent;

class AmazonPayUpdateCv1ToCv2 extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'amazon_pay_update_cv1_to_cv2';

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
        $this->info('amazon_pay_update_cv1_to_cv2');
        $this->amazonPayUpdateComponent->cv1_to_cv2();
    }
}
