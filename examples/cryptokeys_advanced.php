<?php

require __DIR__.'/../vendor/autoload.php';

require __DIR__.'/CliLogger.php';

// This example will create cryptokeys in an existing zone with custom settings.

use Exonet\Powerdns\Powerdns;

$domain = 'dns-new-zone-test.nl';

// Update the key to the real PowerDNS API Key.
$powerdns = new Powerdns('127.0.0.1', 'very_secret_secret');

// Uncomment this line to see what happens when executing this example on the command line.
// $powerdns->setLogger(new CliLogger());

$cryptokeys = $powerdns->cryptokeys($domain);

/*
 * You can specify the arguments PowerDNS uses when generating a new private key. The following example will create a
 * private key based on the RSA SHA512 algorithm with a key length of 4096 bits.
 */
$cryptokeys->configurePrivateKey('RSASHA512', 4096)->create(true);

// You can also use an existing private key (example key from the PowerDNS unit tests):
$existingPrivateKey = "Private-key-format: v1.2\n"
    ."Algorithm: 8 (RSASHA256)\n"
    ."Modulus: 4GlYLGgDI7ohnP8SmEW8EBERbNRusDcg0VQda/EPVHU=\n"
    ."PublicExponent: AQAB\n"
    ."PrivateExponent: JBnuXF5zOtkjtSz3odV+Fk5UNUTTeCsiI16dkcM7TVU=\n"
    ."Prime1: /w7TM4118RoSEvP8+dgnCw==\n"
    ."Prime2: 4T2KhkYLa3w7rdK3Cb2ifw==\n"
    ."Exponent1: 3aeKj9Ct4JuhfWsgPBhGxQ==\n"
    ."Exponent2: tfh1OMPQKBdnU6iATjNR2w==\n"
    ."Coefficient: eVrHe/kauqOewSKndIImrg==)\n";

// Create and activate a new cryptokey with the given private key:
$cryptokeys->setPrivateKey($existingPrivateKey)->create(true);
