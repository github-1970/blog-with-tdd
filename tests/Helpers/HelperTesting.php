<?php

namespace Tests\Helpers;

use Illuminate\Support\Facades\DB;

class HelperTesting
{
    public static function truncateTable($table)
    {
        if(DB::table($table)->count()){
            DB::table($table)->truncate();
        }
    }
}
