<?php

namespace App\Contexts;

use Illuminate\Support\Facades\Log;

class DataBaseMigrationComponent
{
    public static function migrate_exec(): void
    {
        Log::info("start database migrate execution");
    }
}
