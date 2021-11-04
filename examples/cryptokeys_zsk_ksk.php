<?php

require __DIR__.'/../vendor/autoload.php';

require __DIR__.'/CliLogger.php';

// This example will create ZSK and KSK keys for an existing zone.

use Exonet\Powerdns\Powerdns;

$domain = 'dns-new-zone-test.nl';

// Update the key to the real PowerDNS API Key.
$powerdns = new Powerdns('127.0.0.1', 'very_secret_secret');

// Uncomment this line to see what happens when executing this example on the command line.
// $powerdns->setLogger(new CliLogger());

$cryptokeys = $powerdns->cryptokeys($domain);

// Create a ZSK key for this zone:
$cryptokeys->create(true, 'zsk');

// After creating a ZSK key it is possible to add a KSK key.
$cryptokeys->create(true, 'ksk');
