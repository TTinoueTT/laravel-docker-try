<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Contexts\DataBaseMigrationComponent;

class DataBaseMigrate extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:db-migrate {--sort=desc} {--id=*}';

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
        $sort = $this->option('sort');
        $idList = $this->option('id');

        $this->dbMigrationComponent->migrate_exec($sort, $idList);
    }
}
