<?php

namespace Exonet\Powerdns;

use Exonet\Powerdns\Resources\Zone as ZoneResource;
use Exonet\Powerdns\Transformers\CreateZoneTransformer;
use Psr\Log\LoggerInterface;
use Psr\Log\NullLogger;

class Powerdns
{
    /**
     * The version of this package. This is being used for the user-agent header.
     */
    public const CLIENT_VERSION = 'v1.0.1';

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
    private $port = 8001;

    /**
     * @var string The PowerDNS API key.
     */
    private $apiKey;

    /**
     * @var string The PowerDNS server to use.
     */
    private $server = 'localhost';

    /**
     * @var Connector The PowerDNS Connector to make calls.
     */
    private $connector;

    /**
     * PowerDNS Client constructor.
     *
     * @param null|string    $host      (optional) The PowerDNS host. Must include protocol (http, https, etc.).
     * @param null|string    $apiKey    (optional) The PowerDNS API key.
     * @param int|null       $port      (optional) The PowerDNS API Port.
     * @param null|string    $server    (optional) The PowerDNS server to use.
     * @param Connector|null $connector (optional) The Connector to make calls.
     */
    public function __construct(
        ?string $host = null,
        ?string $apiKey = null,
        ?int $port = null,
        ?string $server = null,
        ?Connector $connector = null
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
     * Configure a new connection to a PowerDNS server.
     *
     * @param string $host   The PowerDNS host. Must include protocol (http, https, etc.).
     * @param int    $port   The PowerDNS API Port.
     * @param string $server The PowerDNS server to use.
     *
     * @return Powerdns The created PowerDNS client.
     */
    public function connect(string $host, int $port = 8001, string $server = 'localhost'): self
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
     * @return $this The current client.
     */
    public function useKey(string $key): self
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
     * @return Zone[] Array containing the zones
     */
    public function listZones(): array
    {
        return array_map(
            function (array $args) {
                return new Zone($this->connector, $args['id']);
            },
            $this->connector->get('zones')
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
     * @return self The current client instance.
     */
    public function setLogger(LoggerInterface $log): self
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
