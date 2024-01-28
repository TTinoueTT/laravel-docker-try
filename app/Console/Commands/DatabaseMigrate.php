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
    protected $signature = 'app:db-migrate';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Execute the process of migrating the database';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Hello, World!');
        DataBaseMigrationComponent::migrate_exec();
    }
}
