<?php

namespace Exonet\Powerdns;

use Exonet\Powerdns\Resources\SearchResult;
use Exonet\Powerdns\Resources\SearchResultSet;
use Exonet\Powerdns\Resources\Zone as ZoneResource;
use Exonet\Powerdns\Transformers\CreateZoneTransformer;
use LogicException;
use Psr\Log\LoggerInterface;
use Psr\Log\NullLogger;

class Powerdns implements PowerdnsInterface
{
    /**
     * The version of this package. This is being used for the user-agent header.
     */
    public const CLIENT_VERSION = 'v0.1.0';

    /**
     * @var Powerdns The client instance.
     */
    private static $_instance;

    /**
     * @var LoggerInterface The logger instance.
     */
    private $logger;

    /**
     * @var string The PowerDNS host. Must include protocol (http, https, etc.).
     */
    private $host;

    /**
     * @var int The PowerDNS API Port.
     */
    private $port = 8081;

    /**
     * @var string The PowerDNS API key.
     */
    private $apiKey;

    /**
     * @var string The PowerDNS server to use.
     */
    private $server = 'localhost';

    /**
     * @var ConnectorInterface The PowerDNS Connector to make calls.
     */
    private $connector;

    /**
     * PowerDNS Client constructor.
     *
     * @param string|null             $host      (optional) The PowerDNS host. Must include protocol (http, https, etc.).
     * @param string|null             $apiKey    (optional) The PowerDNS API key.
     * @param int|null                $port      (optional) The PowerDNS API Port.
     * @param string|null             $server    (optional) The PowerDNS server to use.
     * @param ConnectorInterface|null $connector (optional) The Connector to make calls.
     */
    public function __construct(
        ?string $host = null,
        ?string $apiKey = null,
        ?int $port = null,
        ?string $server = null,
        ?ConnectorInterface $connector = null
    ) {
        if (self::$_instance === null) {
            self::$_instance = $this;
        }

        if ($host !== null) {
            $this->host = $host;
        }

        if ($apiKey !== null) {
            $this->apiKey = $apiKey;
        }

        if ($port !== null) {
            $this->port = $port;
        }

        if ($server !== null) {
            $this->server = $server;
        }

        $this->connector = $connector ?? new Connector($this);
    }

    /**
     * Set the configured connector instance instead of the default one.
     *
     * @param ConnectorInterface $connector The connector instance to use.
     *
     * @return $this The current Powerdns class.
     */
    public function setConnector(ConnectorInterface $connector): self
    {
        $this->connector = $connector;

        return $this;
    }

    /**
     * Configure a new connection to a PowerDNS server.
     *
     * @param string $host   The PowerDNS host. Must include protocol (http, https, etc.).
     * @param int    $port   The PowerDNS API Port.
     * @param string $server The PowerDNS server to use.
     *
     * @return PowerdnsInterface The created PowerDNS client.
     */
    public function connect(string $host, int $port = 8081, string $server = 'localhost'): PowerdnsInterface
    {
        $this->host = $host;
        $this->port = $port;
        $this->server = $server;

        return $this;
    }

    /**
     * Set the authorization key to use for each request.
     *
     * @param string $key The key to use.
     *
     * @return PowerdnsInterface The current client.
     */
    public function useKey(string $key): PowerdnsInterface
    {
        $this->apiKey = $key;

        return $this;
    }

    /**
     * Create a new zone.
     *
     * @param string $canonicalDomain The canonical domain name.
     * @param array  $nameservers     The name servers.
     * @param bool   $useDnssec       (Default: false) When true use DNSSEC for this zone.
     *
     * @return Zone The created Zone.
     */
    public function createZone(string $canonicalDomain, array $nameservers, bool $useDnssec = false): Zone
    {
        $fixDot = substr($canonicalDomain, -1) !== '.';

        if ($fixDot) {
            $canonicalDomain .= '.';
        }

        $newZone = new ZoneResource();
        $newZone->setName($canonicalDomain);
        $newZone->setNameservers($nameservers);
        $newZone->setDnssec($useDnssec);
        $postData = new CreateZoneTransformer($newZone);

        $this->connector->post('zones', $postData);

        return $this->zone($canonicalDomain);
    }

    /**
     * Create a new zone based on a zone resource.
     *
     * @param ZoneResource $zoneResource The zone resource.
     *
     * @return Zone The created zone.
     */
    public function createZoneFromResource(ZoneResource $zoneResource): Zone
    {
        $this->connector->post('zones', new CreateZoneTransformer($zoneResource));

        return $this->zone($zoneResource->getName());
    }

    /**
     * Get a zone instance to work with.
     *
     * @param string $canonicalDomain The canonical domain name of the zone.
     *
     * @return Zone The zone.
     */
    public function zone(string $canonicalDomain): Zone
    {
        return new Zone($this->connector, $canonicalDomain);
    }

    /**
     * Remove a zone.
     *
     * @param string $canonicalDomain The canonical domain name of the zone to remove.
     *
     * @return bool True that the zone was removed.
     */
    public function deleteZone(string $canonicalDomain): bool
    {
        $this->connector->delete('zones/'.$canonicalDomain);

        return true;
    }

    /**
     * Retrieve all zones.
     *
     * @param bool $omitDnssecAndEditedSerialFields When set to true dnssec and edited_serial are omitted
     *
     * @return Zone[] Array containing the zones
     *
     * @see https://doc.powerdns.com/authoritative/http-api/zone.html#get--servers-server_id-zones
     */
    public function listZones(bool $omitDnssecAndEditedSerialFields = false): array
    {
        return array_map(
            function (array $args) {
                return new Zone($this->connector, $args['id']);
            },
            $this->connector->get('zones?dnssec='.($omitDnssecAndEditedSerialFields ? 'true' : 'false'))
        );
    }

    /**
     * Get a cryptokey instance to work with.
     *
     * @param string $canonicalDomain The canonical domain name of the zone.
     *
     * @return Cryptokey The cryptokey instance.
     */
    public function cryptokeys(string $canonicalDomain): Cryptokey
    {
        return new Cryptokey($this->connector, $canonicalDomain);
    }

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
    public function statistics($statistic = null, $includeRings = false): array
    {
        // Convert $includeRings param to string.
        $includeRings = $includeRings ? 'true' : 'false';

        $endpoint = 'statistics?includerings='.$includeRings;

        // Request a specific statistic.
        if ($statistic) {
            $endpoint .= '&statistic='.$statistic;
        }

        return $this->connector->get($endpoint);
    }

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
    public function search(string $query, int $size = 100, string $type = 'all'): SearchResultSet
    {
        if (!in_array($type, ['all', 'zone', 'record', 'comment'])) {
            throw new LogicException('Invalid search type given. Type must be one of "all", "zone", "record" or "comment".');
        }

        if ($size < 1) {
            throw new LogicException('Invalid search size given. Must be at least 1.');
        }

        $response = $this->connector->get(
            sprintf(
                'search-data?q=%s&max=%d&object_type=%s',
                urlencode($query),
                $size,
                $type
            )
        );

        $searchResults = array_map(static function ($item) { return new SearchResult($item); }, $response);

        return new SearchResultSet($searchResults);
    }

    /**
     * Get the PowerDNS server version.
     *
     * @return string The server version.
     */
    public function serverVersion(): string
    {
        return $this->connector->get('/')['version'];
    }

    /**
     * Get the logger instance.
     *
     * @return LoggerInterface The log instance.
     */
    public function log(): LoggerInterface
    {
        if ($this->logger === null) {
            // If there's no logger set, use the NullLogger.
            $this->logger = new NullLogger();
        }

        return $this->logger;
    }

    /**
     * Set the logger instance to use.
     *
     * @param LoggerInterface $log The log instance to use.
     *
     * @return PowerdnsInterface The current client instance.
     */
    public function setLogger(LoggerInterface $log): PowerdnsInterface
    {
        $this->logger = $log;

        return $this;
    }

    /**
     * Get the client config items.
     *
     * @return mixed[] Array containing the client config items.
     */
    public function getConfig(): array
    {
        return [
            'host' => $this->host,
            'port' => $this->port,
            'server' => $this->server,
            'apiKey' => $this->apiKey,
        ];
    }
}
