<?php

use Illuminate\Support\Facades\DB;



test('database connection is sqlite', function () {
    $this->assertEquals('sqlite', DB::connection()->getDriverName());
    $this->assertEquals(':memory:', \DB::connection()->getDatabaseName());
});
