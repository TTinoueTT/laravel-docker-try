<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Contexts\DataBaseMigrationComponent;

class DataBaseMigrate extends Command
{
    /**
     * The name and signature of the console command.
     * --execMode
     * 1 => 退会済みレコード
     * 2 => 1以外のレコード
     * ex. sail artisan app:db-migrate --execMode=2 --sort=asc
     * ex. sail artisan app:db-migrate --execMode=1 --id=4815
     *
     * @var string
     */
    protected $signature = 'app:db-migrate {--execMode=1} {--sort=desc} {--id=*}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Execute the process of migrating the database';

    private $dbMigrationComponent;

    public function __construct(DataBaseMigrationComponent $dbMigrationComponent)
    {
        parent::__construct();
        $this->dbMigrationComponent = $dbMigrationComponent;
    }

    /**
     * Execute the console command.
     */
    public function handle(): void
    {
        $this->info('app:db-migrate');
        $execMode = $this->option('execMode');
        $sort = $this->option('sort');
        $idList = $this->option('id');

        $this->dbMigrationComponent->migrate_exec($execMode, $sort, $idList);
    }
}
