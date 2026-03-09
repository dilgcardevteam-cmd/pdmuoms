<?php
// Test the municipality projects API directly through Laravel
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';

// Get the router and call the route directly
$kernel = $app->make(\Illuminate\Contracts\Http\Kernel::class);
$request = \Illuminate\Http\Request::create('/api/municipality-projects', 'GET');
$response = $kernel->handle($request);

if ($response->status() === 200) {
    $data = json_decode($response->getContent(), true);
    echo "Total features: " . count($data['features']) . "\n";
    echo "Max count: " . $data['maxCount'] . "\n";
    echo "\nFirst 5 features:\n";
    foreach (array_slice($data['features'], 0, 5) as $feature) {
        echo $feature['properties']['name'] . " - " . $feature['properties']['project_count'] . " projects\n";
    }
} else {
    echo "Status: " . $response->status() . "\n";
    echo "Content:\n" . $response->getContent() . "\n";
}
