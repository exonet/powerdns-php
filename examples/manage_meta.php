<?php

require __DIR__.'/../vendor/autoload.php';

require __DIR__.'/CliLogger.php';

use Exonet\Powerdns\Exceptions\PowerdnsException;
use Exonet\Powerdns\MetaType;
use Exonet\Powerdns\Powerdns;
use Exonet\Powerdns\RecordType;

$domain = 'zone-meta-test.nl';
$nameServers = ['ns1.example.com.', 'ns2.example.eu.'];
$dnsRecords = [
    ['name' => '@', 'type' => RecordType::A, 'content' => '127.0.0.1', 'ttl' => 60],
    ['name' => 'www', 'type' => RecordType::A, 'content' => '127.0.0.1', 'ttl' => 60],
    ['name' => '@', 'type' => RecordType::AAAA, 'content' => '2a00:1e28:3:1629::1', 'ttl' => 60],
];

// Update the key to the real PowerDNS API Key.
$powerdns = new Powerdns('127.0.0.1', 'very_secret_secret');

// Uncomment this line to see what happens when executing this example on the command line.
// $powerdns->setLogger(new CliLogger());

// Delete the zone if it already exists so this example can be executed multiple times.
try {
    $powerdns->deleteZone($domain);
} catch (PowerdnsException $e) {
}

// Create a new zone with the defined records and name servers.
$powerdns->createZone($domain, $nameServers)->create($dnsRecords);

// Get the zone.
$zone = $powerdns->zone($domain);

echo 'EXISTING META RECORDS:'.PHP_EOL;
echo str_repeat('-', 40).PHP_EOL;
foreach ($zone->meta()->get() as $metaItem) {
    echo $metaItem->getKind().': '.implode('; ', $metaItem->getData()).PHP_EOL;
}

// Create a new meta record.
$result = $zone->meta()->create('X-Example', 'test');

// Create a new meta record with multiple values.
$data = ['example.com', 'example.org'];
$zone->meta()->create(MetaType::FORWARD_DNSUPDATE, $data);

echo PHP_EOL.'NEW META RECORDS:'.PHP_EOL;
echo str_repeat('-', 40).PHP_EOL;
$metaSet = $zone->meta()->get();
foreach ($metaSet as $metaItem) {
    echo $metaItem->getKind().': '.implode('; ', $metaItem->getData()).PHP_EOL;
}

// Get the first meta record.
$record = $metaSet[1];

// Update the meta record.
$record->setData(['test', 'test2'])->save();

// Delete the meta record.
$record->delete();

// Delete a specific meta record.
$zone->meta()->get(MetaType::SOA_EDIT)->delete();

echo PHP_EOL.'UPDATED RECORDS:'.PHP_EOL;
echo str_repeat('-', 40).PHP_EOL;
foreach ($zone->meta()->get() as $metaItem) {
    echo $metaItem->getKind().': '.implode('; ', $metaItem->getData()).PHP_EOL;
}
