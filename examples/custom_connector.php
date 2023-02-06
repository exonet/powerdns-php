<?php

require __DIR__.'/../vendor/autoload.php';

require __DIR__.'/CliLogger.php';
require __DIR__.'/BasicAuthConnector.php';

// This example will create a new domain and adds some default DNS records.

use Exonet\Powerdns\Powerdns;
use Exonet\Powerdns\RecordType;

$domain = 'dns-new-zone-test.nl';
$nameServers = ['ns1.example.com.', 'ns2.example.eu.'];
$dnsRecords = [
    ['name' => '@', 'type' => RecordType::A, 'content' => '127.0.0.1', 'ttl' => 60],
    ['name' => 'www', 'type' => RecordType::A, 'content' => '127.0.0.1', 'ttl' => 60],
];

try {
    // Update the key to the real PowerDNS API Key.
    $powerdns = new Powerdns('127.0.0.1', 'secret', 8045);

    // Set a custom connector.
    $powerdns->setConnector(new BasicAuthConnector($powerdns, null, 'this-is-me', 'fancy-password'));

    // Uncomment this line to see what happens when executing this example on the command line.
    $powerdns->setLogger(new CliLogger());

    // Uncomment this line if you want to run this example multiple times.
    // $powerdns->deleteZone($domain);

    // Create a new zone with the defined records and name servers.
    $powerdns->createZone($domain, $nameServers)->create($dnsRecords);
} catch (Exception $exception) {
    print_r($exception);
}
