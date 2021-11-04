<?php

require __DIR__.'/../vendor/autoload.php';

require __DIR__.'/CliLogger.php';

// This example will create, activate, deactivate and delete Crypto Keys for DNSSEC in an existing zone.

use Exonet\Powerdns\Powerdns;

$domain = 'dns-new-zone-test.nl';

// Update the key to the real PowerDNS API Key.
$powerdns = new Powerdns('127.0.0.1', 'very_secret_secret');

// Uncomment this line to see what happens when executing this example on the command line.
$powerdns->setLogger(new CliLogger());

// Example to get the cryptokeys from a zone:
// $dnsSec = $powerDns->zone($domain)->cryptokeys();

// Or, if you've the zone name, but there's no need to use the Zone class:
$cryptokeys = $powerdns->cryptokeys($domain);

// Create a key for the zone:
$newKey = $cryptokeys->create();

// Make this key active. If you don't pass ID(s) to the 'setActive' method, all inactive keys will also be activated:
$cryptokeys->setActive(true, $newKey->getId());

// It is also possible to take a 'shortcut' to create and activate a key:
$cryptokeys->create(true);

// Get all the cryptokeys and deactivate the first found one:
$cryptokeys->setActive(false, $cryptokeys->getKeys()[0]->getId());

// Remove all inactive keys:
$cryptokeys->deleteInactive();

// Only the second created cryptokey should be available:
count($cryptokeys->getKeys()) === 1 ?: exit('More than one key found!');

// Disable all keys (and thus DNSSEC). By omitting ID(s), this action is applied to ALL keys:
$cryptokeys->setActive(false);

// Remove all keys:
$cryptokeys->deleteInactive();
