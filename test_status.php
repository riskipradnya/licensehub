<?php
require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$licenses = \App\Models\License::all();
foreach ($licenses as $l) {
    echo "ID: {$l->id} | Name: {$l->name} | DB Status: {$l->getRawOriginal('status')} | Eloquent Status: {$l->status} | Computed Days: {$l->days_until_expiry}\n";
}
