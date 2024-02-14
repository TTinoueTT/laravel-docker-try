<?php

namespace App\Services;

use App\Models\BaseModel;
use App\Models\Next\NextBookmark;
use App\Models\Old\OldUser;
use App\Models\Next\NextUser;
use App\Models\Next\NextUserAnalysis;
use App\Models\Old\OldBookmark;
use App\Services\IMigrateService;

use Illuminate\Support\Facades\Log;

final class BookmarkService implements IMigrateService
{
    public function migrateOldToNew(BaseModel $oldUser)
    {
    }

    public function migrateOldToNewWithNew(BaseModel $oldUser, NextUser $nextUser)
    {
        $oldBookmarks = $oldUser->bookmarks()->get();
        foreach ($oldBookmarks as $old) {
            $new = new NextBookmark();
            $new->user_id = $nextUser->id;
            $new = $this->oldToNew($old, $new);

            $exists = NextBookmark::where('user_id', $new->user_id)
                ->where('itemcd', $new->itemcd)
                ->where('content_key', $new->content_key)
                ->exists();

            if ($exists) {
                Log::info("A record with the same user_id, itemcd, content_key combination already exists, skipping save.");
                continue;
            }

            Log::info("Start save to {$new->getTable()}");
            if ($new->save()) {
                Log::info("saved successfully.", ['bookmark id' => $new->id]);
            } else {
                Log::error("Failed to save the bookmark.");
            }
        }

        Log::info($oldBookmarks->isEmpty() ? "Not exist bookmark data \/(´；ω；`;)\/" : "bookmark migrate process is finish !!!");
    }

    private function oldToNew(OldBookmark $old, NextBookmark $new): NextBookmark
    {
        $new->itemcd = $old->itemcd;
        $new->content_key = "NMA";
        $new->created_at = $old->created_at;
        $new->updated_at = $old->updated_at;

        return $new;
    }
}
