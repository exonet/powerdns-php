<?php

namespace Exonet\Powerdns;

use Exonet\Powerdns\Resources\Zone as ZoneResource;

abstract class AbstractZone
{
    /**
     * @var string The zone to use.
     */
    protected $zone;

    /**
     * @var ZoneResource The zone resource.
     */
    protected $zoneResource;

    /**
     * @var Connector The PowerDNS Connector to make calls.
     */
    protected $connector;

    /**
     * The class constructor.
     *
     * @param Connector   $connector       The zone to use.
     * @param null|string $canonicalDomain The PowerDNS Connector to make calls.
     */
    public function __construct(Connector $connector, ?string $canonicalDomain = null)
    {
        $this->connector = $connector;

        if ($canonicalDomain !== null) {
            $this->setZone($canonicalDomain);
        }
    }

    /**
     * Set the zone to use.
     *
     * @param string $canonicalDomain The zone to use.
     *
     * @return $this The current class instance.
     */
    public function setZone(string $canonicalDomain) : self
    {
        $fixDot = substr($canonicalDomain, -1) !== '.';

        if ($fixDot) {
            $canonicalDomain .= '.';
        }

        $this->zone = $canonicalDomain;

        return $this;
    }

    /**
     * Get the zone resource.
     *
     * @return ZoneResource The zone resource.
     */
    public function resource() : ZoneResource
    {
        if ($this->zoneResource === null) {
            $zoneData = $this->connector->get($this->getZonePath());
            $zoneResource = new ZoneResource();
            $zoneResource->setApiResponse($zoneData);

            $this->zoneResource = $zoneResource;
        }

        return $this->zoneResource;
    }

    /**
     * Get the zone path for API calls.
     *
     * @param null|string $path The path to append to the zone.
     *
     * @return string The API zone path.
     */
    protected function getZonePath(?string $path = null) : string
    {
        return sprintf('zones/%s%s', $this->zone, $path);
    }
}
