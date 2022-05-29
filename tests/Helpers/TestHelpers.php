<?php

namespace Tests\Helpers;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class TestHelpers
{
    public static function truncateTable($table)
    {

        is_array($table) ? self::truncateManyTables($table) : self::truncateOneTable($table);

    }

    private static function truncateManyTables($tables)
    {
        foreach ($tables as $table) {
            if(DB::table($table)->count()){
                Schema::disableForeignKeyConstraints();
                DB::table($table)->truncate();
                Schema::enableForeignKeyConstraints();
            }
        }
    }

    private static function truncateOneTable($table)
    {
        if(DB::table($table)->count()){
            self::repeatedWorksForTruncateTable($table);
        }
    }

    private static function repeatedWorksForTruncateTable($table)
    {
        Schema::disableForeignKeyConstraints();
        DB::table($table)->truncate();
        Schema::enableForeignKeyConstraints();
    }
}
