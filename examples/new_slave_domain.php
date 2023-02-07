<?php

require __DIR__.'/../vendor/autoload.php';

require __DIR__.'/CliLogger.php';

/*
 * This example will create a new slave domain.
 * The slave domain will get serial 0 at first.
 * PowerDNS will fetch the zone records and serial from the master using AXFR.
 */

use Exonet\Powerdns\Powerdns;
use Exonet\Powerdns\Resources\Zone as ZoneResource;

$canonicalDomain = 'slave-zone-test.nl.';
$masters = ['1.1.1.1'];

// Update the key to the real PowerDNS API Key.
$powerdns = new Powerdns('127.0.0.1', 'very_secret_secret');

// Uncomment this line to see what happens when executing this example on the command line.
// $powerdns->setLogger(new CliLogger());

// Uncomment this line if you want to run this example multiple times.
// $powerdns->deleteZone($canonicalDomain);

// Create a new zone resource.
$newZone = new ZoneResource();
$newZone->setName($canonicalDomain);
$newZone->setKind('Slave');
$newZone->setMasters($masters);

// Create a new zone with the defined records and name servers.
$powerdns->createZoneFromResource($newZone);
