<?php

require __DIR__.'/../vendor/autoload.php';
require __DIR__.'/CliLogger.php';

/*
 * This example will create a new domain and adds some default DNS records.
 */

use Exonet\Powerdns\Powerdns;
use Exonet\Powerdns\RecordType;

$domain = 'dns-new-zone-test.nl';
$nameServers = ['ns1.example.com.', 'ns2.example.'];
$dnsRecords = [
    ['name' => '@', 'type' => RecordType::A, 'content' => '127.0.0.1', 'ttl' => 60],
    ['name' => 'www', 'type' => RecordType::A, 'content' => '127.0.0.1', 'ttl' => 60],
    ['name' => 'mail01', 'type' => RecordType::A, 'content' => '127.0.0.1'],
    ['name' => 'mail02', 'type' => RecordType::A, 'content' => '127.0.0.2'],

    ['name' => '@', 'type' => RecordType::AAAA, 'content' => '2a00:1e28:3:1629::1', 'ttl' => 60],
    ['name' => 'www', 'type' => RecordType::AAAA, 'content' => '2a00:1e28:3:1629::1', 'ttl' => 60],
    ['name' => 'mail01', 'type' => RecordType::AAAA, 'content' => '2a00:1e28:3:1629::2'],
    ['name' => 'mail02', 'type' => RecordType::AAAA, 'content' => '2a00:1e28:3:1629::3'],

    ['name' => '@', 'type' => RecordType::MX, 'content' => ['10 mail01.@', '20 mail02.@']],
    ['name' => '@', 'type' => RecordType::TXT, 'content' => '"v=spf1 a mx include:_spf.example.com ?all"'],
];

// Update the key to the real PowerDNS API Key.
$powerdns = new Powerdns('127.0.0.1', 'very_secret_secret');

// Uncomment this line to see what happens when executing this example on the command line.
// $powerdns->setLogger(new CliLogger());

// Uncomment this line if you want to run this example multiple times.
// $powerdns->deleteZone($domain);

// Create a new zone with the defined records and name servers.
$powerdns->createZone($domain, $nameServers)->create($dnsRecords);
