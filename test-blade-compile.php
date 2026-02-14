<?php

echo "Testing Blade compilation...\n";

require __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

try {
    $rendered = view('tenant.pages.people.show', ['code' => 'MQ'])->render();
    echo "✅ OK - View compiled successfully\n";
    echo "Rendered length: " . strlen($rendered) . " bytes\n";
} catch (\Exception $e) {
    echo "❌ ERROR: " . $e->getMessage() . "\n";
    echo "File: " . $e->getFile() . "\n";
    echo "Line: " . $e->getLine() . "\n";
    echo "\nStack trace:\n";
    echo $e->getTraceAsString() . "\n";
}
