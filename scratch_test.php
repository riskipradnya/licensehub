<?php
require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();
$types = [
    'App\Notifications\LicenseAlertNotification',
    'App\Notifications\LicenseResolvedNotification',
    'App\Notifications\LicenseExpiringNotification'
];
foreach($types as $t) {
    echo "TYPE: $t\n";
    $n = Illuminate\Notifications\DatabaseNotification::where('type', $t)->first();
    if ($n) {
        print_r($n->data);
    }
    echo "---------------\n";
}
