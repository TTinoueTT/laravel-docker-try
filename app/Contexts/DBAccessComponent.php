<?php

namespace App\Contexts;

use App\Enums\LocationType;
// use Illuminate\Support\Facades\DB;

class DBAccessComponent
{

    public function getDatabaseName()
    {
        return "default_database";
    }


    /**
     * Undocumented function
     *
     * @param [type] $location
     * @return void
     */
    public function getHost($location)
    {
        switch ($location) {
            case LocationType::DEVELOPMENT:
                return [
                    "new" => config("database.connections.mysql_new.host"),
                    "new_payment" => config("database.connections.mysql_new_payment.host"),
                    "old" => config("database.connections.mysql_old.host"),
                ];

            case LocationType::PRODUCTION:
                return [
                    "new" => config("database.connections.mysql_new.host"),
                    "new_payment" => config("database.connections.mysql_new_payment.host"),
                    "old" => config("database.connections.mysql_old.host"),
                ];

            default:
                return [
                    "new" => config("database.connections.mysql_new.host"),
                    "new_payment" => config("database.connections.mysql_new_payment.host"),
                    "old" => config("database.connections.mysql_old.host"),
                ];
        }
    }


    public function getPassword($location)
    {
        switch ($location) {
            case LocationType::DEVELOPMENT:
                return [
                    "new" => config("database.connections.mysql_new.password"),
                    "new_payment" => config("database.connections.mysql_new.password"),
                    "old" => config("database.connections.mysql_new.password"),
                ];

            default:
                return [
                    "new" => config("database.connections.mysql_new.password"),
                    "new_payment" => config("database.connections.mysql_new.password"),
                    "old" => config("database.connections.mysql_new.password"),
                ];
        }
    }
}
