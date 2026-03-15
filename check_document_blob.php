<?php
require_once 'vendor/autoload.php';

$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Illuminate\Support\Facades\DB;

// Check the document_blob column type
$result = DB::select("SHOW COLUMNS FROM resources WHERE Field = 'document_blob'");
echo "Document blob column info:\n";
print_r($result);

// Check if there's any data in the table
$count = DB::select("SELECT COUNT(*) as count FROM resources WHERE document_blob IS NOT NULL");
echo "\nNumber of resources with documents: " . $count[0]->count . "\n";

// Check a sample record if exists
$sample = DB::select("SELECT id, document_name, document_mime, LENGTH(document_blob) as blob_size FROM resources WHERE document_blob IS NOT NULL LIMIT 1");
if ($sample) {
    echo "\nSample document record:\n";
    print_r($sample);
}
