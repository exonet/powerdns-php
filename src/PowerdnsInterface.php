<?php

declare(strict_types=1);

namespace Exonet\Powerdns;

use Exonet\Powerdns\Resources\SearchResultSet;
use Exonet\Powerdns\Resources\Zone as ZoneResource;
use Psr\Log\LoggerInterface;

interface PowerdnsInterface
{
    /**
     * Configure a new connection to a PowerDNS server.
     *
     * @param string $host   The PowerDNS host. Must include protocol (http, https, etc.).
     * @param int    $port   The PowerDNS API Port.
     * @param string $server The PowerDNS server to use.
     *
     * @return Powerdns The created PowerDNS client.
     */
    public function connect(string $host, int $port = 8081, string $server = 'localhost'): self;

    /**
     * Set the authorization key to use for each request.
     *
     * @param string $key The key to use.
     *
     * @return PowerdnsInterface The current client.
     */
    public function useKey(string $key): self;

    /**
     * Create a new zone.
     *
     * @param string $canonicalDomain The canonical domain name.
     * @param array  $nameservers     The name servers.
     * @param bool   $useDnssec       (Default: false) When true use DNSSEC for this zone.
     *
     * @return Zone The created Zone.
     */
    public function createZone(string $canonicalDomain, array $nameservers, bool $useDnssec = false): Zone;

    /**
     * Create a new zone based on a zone resource.
     *
     * @param ZoneResource $zoneResource The zone resource.
     *
     * @return Zone The created zone.
     */
    public function createZoneFromResource(ZoneResource $zoneResource): Zone;

    /**
     * Get a zone instance to work with.
     *
     * @param string $canonicalDomain The canonical domain name of the zone.
     *
     * @return Zone The zone.
     */
    public function zone(string $canonicalDomain): Zone;

    /**
     * Remove a zone.
     *
     * @param string $canonicalDomain The canonical domain name of the zone to remove.
     *
     * @return bool True that the zone was removed.
     */
    public function deleteZone(string $canonicalDomain): bool;

    /**
     * Retrieve all zones.
     *
     * @param bool $includeDnssecAndEditedSerialFields When set to true dnssec and edited_serial are omitted
     *
     * @return Zone[] Array containing the zones
     *
     * @see https://doc.powerdns.com/authoritative/http-api/zone.html#get--servers-server_id-zones
     */
    public function listZones(bool $includeDnssecAndEditedSerialFields = false): array;

    /**
     * Get a cryptokey instance to work with.
     *
     * @param string $canonicalDomain The canonical domain name of the zone.
     *
     * @return Cryptokey The cryptokey instance.
     */
    public function cryptokeys(string $canonicalDomain): Cryptokey;

    /**
     * Query PowerDNS internal statistics.
     * The ring statistics are disabled by default to speedup the request and reduce the response size.
     *
     * The $statistics and $includeRings parameters are supported in PowerDNS 4.3 and newer.
     * On older PowerDNS instances these parameters are ignored.
     *
     * @param string|null $statistic    Optional name of a specific statistic to get.
     * @param bool        $includeRings Include ring statistics or not.
     *
     * @return array An array with statistics.
     */
    public function statistics($statistic = null, $includeRings = false): array;

    /**
     * Search for the specified string in zones, records, comments or in all three. The * character can be used in the
     * query as a wildcard character and the ? character can be used as a wildcard for a single character.
     *
     * @param string $query The string to search for.
     * @param int    $size  The maximum number of returned results.
     * @param string $type  The search type. Can be 'all', 'zone', 'record' or 'comment'.
     *
     * @return SearchResultSet A collection with search results.
     */
    public function search(string $query, int $size = 100, string $type = 'all'): SearchResultSet;

    /**
     * Get the PowerDNS server version.
     *
     * @return string The server version.
     */
    public function serverVersion(): string;

    /**
     * Get the logger instance.
     *
     * @return LoggerInterface The log instance.
     */
    public function log(): LoggerInterface;

    /**
     * Set the logger instance to use.
     *
     * @param LoggerInterface $log The log instance to use.
     *
     * @return PowerdnsInterface The current client instance.
     */
    public function setLogger(LoggerInterface $log): PowerdnsInterface;

    /**
     * Get the client config items.
     *
     * @return mixed[] Array containing the client config items.
     */
    public function getConfig(): array;
}
