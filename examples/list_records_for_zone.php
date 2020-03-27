<?php

require __DIR__.'/../vendor/autoload.php';
require __DIR__.'/CliLogger.php';

/*
 * This example will create a new domain and adds some default DNS records.
 */

use Exonet\Powerdns\Powerdns;
use Exonet\Powerdns\Resources\Record;

$domain = 'dns-new-zone-test.nl';

// Update the key to the real PowerDNS API Key.
$powerdns = new Powerdns('127.0.0.1', 'very_secret_secret');

// Uncomment this line to see what happens when executing this example on the command line.
// $powerdns->setLogger(new CliLogger());

echo PHP_EOL;

// Get all the resource records for the specified zone/domain.
$zoneResourceSets = $powerdns->zone($domain)->get();

// Loop through all found resource records.
foreach ($zoneResourceSets as $resourceSet) {
    /*
     * A resource set can contain multiple records. By mapping them it is possible
     * to extract only the content, which is the only necessary information for this example.
     */
    $recordContents = array_map(function (Record $record) {
        return $record->getContent();
    }, $resourceSet->getRecords());

    // Show for each resource set in the zone the type, name and values.
    echo sprintf(
        '| %-4s | %30s --> %-30s%s',
        $resourceSet->getType(),
        $resourceSet->getName(),
        implode(', ', $recordContents),
        PHP_EOL
    );
}
