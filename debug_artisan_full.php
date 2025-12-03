<?php
require __DIR__.'/vendor/autoload.php';
echo "Autoload loaded\n";
$app = require_once __DIR__.'/bootstrap/app.php';
echo "Bootstrap loaded\n";
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
echo "Kernel made\n";
$status = $kernel->handle(
    $input = new Symfony\Component\Console\Input\ArgvInput,
    new Symfony\Component\Console\Output\ConsoleOutput
);
echo "Command handled\n";
