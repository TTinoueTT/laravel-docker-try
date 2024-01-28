<?php

namespace App\Contexts;

use Illuminate\Support\Str;
use App\Enums\LocationType;

class SuitLocationComponent
{
    public static function get()
    {
        if (Str::contains(url('/', null, true), 'localhost')) {
            return LocationType::LOCAL;
        } else if (Str::contains(url('/', null, true), 'dev') || Str::contains(url('/', null, true), 'test')) {
            return LocationType::DEVELOPMENT;
            // } else if (Str::contains(url('/', null, true), 'rel')) {
            //     return LocationType::REL;
        } else {
            return LocationType::PRODUCTION;
        }
    }
    public static function isLocalhost()
    {
        return SuitLocationComponent::get() == LocationType::LOCAL;
    }
    public static function isDevelopment()
    {
        return SuitLocationComponent::get() == LocationType::DEVELOPMENT;
    }
    // public static function isReleaseable()
    // {
    //     return SuitLocationComponent::get() == LocationType::REL;
    // }
    public static function isProduction()
    {
        return SuitLocationComponent::get() == LocationType::PRODUCTION;
    }
}
