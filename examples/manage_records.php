<?php

require __DIR__.'/../vendor/autoload.php';
require __DIR__.'/CliLogger.php';

/*
 * This example will create, update and delete some DNS records in an existing zone.
 */

use Exonet\Powerdns\Powerdns;
use Exonet\Powerdns\RecordType;
use Exonet\Powerdns\Resources\ResourceRecord;

$domain = 'dns-new-zone-test.nl';

// Update the key to the real PowerDNS API Key.
$powerdns = new Powerdns('127.0.0.1', 'very_secret_secret');

// Uncomment this line to see what happens when executing this example on the command line.
// $powerdns->setLogger(new CliLogger());

// Get an existing zone:
$zone = $powerdns->zone($domain);

// Add two new records to the zone; test2 and test3:
$zone->create('test2', RecordType::A, '127.0.0.2', 3600);
$zone->create('test2', RecordType::AAAA, '::2', 3600);

$zone->create('test3', RecordType::A, '127.0.0.3', 3600);
$zone->create('test3', RecordType::AAAA, '::3', 3600);

/*
 * In PowerDNS there's no such thing as 'updating' a record. Instead, just create a new record with the same name and
 * type, as this is the 'unique' combination for a record. The following example will change the IP for 'test2' from
 * '127.0.0.2' (see the example above) to  '127.0.1.2'.
 */
$zone->create('test2', RecordType::A, '127.0.1.2', 3600);

/*
 * To update the TTL of all 'test2' record first find it in the zone and than you can provide a closure to the 'map'
 * function of the ResourceSet. Calling the 'save' method will change the records in PowerDNS. This example will update
 * the TTL of all 'test2' records from 3600 (see the example above) to 60:
 */
$zone->find('test2')->map(function (ResourceRecord $resourceRecord) {
    return $resourceRecord->setTtl(60);
})->save();

/*
 * To delete a specific record from the zone, perform a search followed by a delete:
 */
$zone->find('test3')->delete();

/*
 * The example above will delete ALL records for 'test3' (in this example both the A and AAAA). If you'd like to remove
 * only a specific type, you can pass a second argument. The example below only removes the AAAA record for 'test2',
 * leaving the A record intact.
 */
$zone->find('test2', RecordType::AAAA)->delete();
