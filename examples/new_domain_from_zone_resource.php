<?php

require __DIR__.'/../vendor/autoload.php';
require __DIR__.'/CliLogger.php';

/*
 * This example will create a new domain based on a zone resource object with some more 'advanced'
 * settings and adds some default DNS records.
 */

use Exonet\Powerdns\Powerdns;
use Exonet\Powerdns\RecordType;
use Exonet\Powerdns\Resources\Zone as ZoneResource;

$canonicalDomain = 'dns-new-zone-resource-test.nl.';
$nameServers = ['ns1.example.com.', 'ns2.example.eu.'];
$dnsRecords = [
    ['name' => '@', 'type' => RecordType::A, 'content' => '127.0.0.1', 'ttl' => 60],
    ['name' => 'www', 'type' => RecordType::A, 'content' => '127.0.0.1', 'ttl' => 60],

    ['name' => '@', 'type' => RecordType::AAAA, 'content' => '2a00:1e28:3:1629::1', 'ttl' => 60],
    ['name' => 'www', 'type' => RecordType::AAAA, 'content' => '2a00:1e28:3:1629::1', 'ttl' => 60],
];

// Update the key to the real PowerDNS API Key.
$powerdns = new Powerdns('127.0.0.1', 'very_secret_secret');

// Uncomment this line to see what happens when executing this example on the command line.
// $powerdns->setLogger(new CliLogger());

// Uncomment this line if you want to run this example multiple times.
// $powerdns->deleteZone($canonicalDomain);

// Create a new zone resource.
$newZone = new ZoneResource();
$newZone->setName($canonicalDomain);
$newZone->setKind('native');
$newZone->setDnssec(true);
$newZone->setSoaEdit('epoch');
$newZone->setSoaEditApi('epoch');
// $newZone->setMasters([...]);
$newZone->setNameservers($nameServers);
$newZone->setApiRectify(false);
$newZone->setNsec3param('1 0 100 1234567890');

// Create a new zone with the defined records and name servers.
$powerdns->createZoneFromResource($newZone)->create($dnsRecords);

