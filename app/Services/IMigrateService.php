<?php

namespace App\Services;

use App\Models\BaseModel;

interface IMigrateService
{
    public function migrateOldToNew(BaseModel $oldData);

    // public function migrateOldToNewWithNew(BaseModel $oldData, BaseModel $new);
}
