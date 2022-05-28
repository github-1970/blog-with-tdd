<?php

namespace Tests\Feature\Database;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

class ConnectionTest extends TestCase
{
    public function test_connection_is_connected()
    {
        $this->assertNotEmpty(DB::connection()->getPdo()->getAttribute(\PDO::ATTR_CONNECTION_STATUS));
        $this->assertInstanceOf(\PDO::class, DB::connection()->getPdo());
    }
}
