<?php

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$app->make(\Illuminate\Contracts\Console\Kernel::class);

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

// Get all tables
$tables = DB::select('SHOW TABLES');
$dbName = env('DB_DATABASE');
$tableKey = "Tables_in_{$dbName}";

$sql = "-- Database schema export for {$dbName}\n";
$sql .= "-- Generated: " . date('Y-m-d H:i:s') . "\n\n";
$sql .= "SET FOREIGN_KEY_CHECKS=0;\n\n";

foreach ($tables as $table) {
    $tableName = $table->$tableKey;
    
    // Drop table if exists
    $sql .= "DROP TABLE IF EXISTS `{$tableName}`;\n";
    
    // Get create table statement
    $createTable = DB::select("SHOW CREATE TABLE `{$tableName}`");
    $sql .= $createTable[0]->{'Create Table'} . ";\n\n";
}

$sql .= "SET FOREIGN_KEY_CHECKS=1;\n";

file_put_contents('schema_export.sql', $sql);
echo "Schema exported to schema_export.sql\n";
echo "Total tables: " . count($tables) . "\n";
