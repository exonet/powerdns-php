<?php

namespace Exonet\Powerdns;

use Exonet\Powerdns\Exceptions\InvalidNsec3Param;
use Exonet\Powerdns\Resources\Record;
use Exonet\Powerdns\Resources\ResourceRecord;
use Exonet\Powerdns\Resources\ResourceSet;
use Exonet\Powerdns\Transformers\DnssecTransformer;
use Exonet\Powerdns\Transformers\Nsec3paramTransformer;
use Exonet\Powerdns\Transformers\RRSetTransformer;
use Exonet\Powerdns\Transformers\Transformer;

class Zone extends AbstractZone
{
    /**
     * Create one or more new resource records in the current zone. If $name is passed as multidimensional array those
     * resource records will be created in a single call to the PowerDNS server. If $name is a string, a single resource
     * record is created.
     *
     * @param mixed[]|string $name    The resource record name.
     * @param string         $type    The type of the resource record.
     * @param mixed[]|string $content The content of the resource record. When passing a multidimensional array,
     *                                multiple records are created for this resource record.
     * @param int            $ttl     The TTL.
     *
     * @throws Exceptions\InvalidRecordType If the given type is invalid.
     *
     * @return bool True when created.
     */
    public function create($name, string $type = '', $content = '', int $ttl = 3600): bool
    {
        if (is_array($name)) {
            foreach ($name as $item) {
                $resourceRecords[] = $this->make($item['name'], $item['type'], $item['content'], $item['ttl'] ?? $ttl);
            }
        } else {
            $resourceRecords = [$this->make($name, $type, $content, $ttl)];
        }

        return $this->patch($resourceRecords);
    }

    /**
     * Patch resource records to the PowerDNS server.
     *
     * @param ResourceRecord[] $resourceRecords Array containing the resource records to patch.
     *
     * @return bool True when successful.
     */
    public function patch(array $resourceRecords): bool
    {
        $result = $this->connector->patch($this->getZonePath(), new RRSetTransformer($resourceRecords));
        // Invalidate the resource.
        $this->zoneResource = null;

        /*
         * The PATCH request will return an 204 No Content, so the $result is empty. If this is the case, the PATCH was
         * successful. If there was an error, an exception will be thrown.
         */
        return empty($result);
    }

    /**
     * Put an updated version of a zone to the PowerDNS server.
     *
     * @param Transformer $transformer a transformer object
     *
     * @return bool True when successful.
     */
    public function put(Transformer $transformer): bool
    {
        $result = $this->connector->put($this->getZonePath(), $transformer);
        // Invalidate the resource.
        $this->zoneResource = null;

        /*
         * The PUT request will return an 204 No Content, so the $result is empty. If this is the case, the PUT was
         * successful. If there was an error, an exception will be thrown.
         */
        return empty($result);
    }

    /**
     * Get all the resource records in the current zone. If $recordType is specified, only get those specific resource
     * records.
     *
     * @param string|null $recordType (optional) The type of resource record.
     *
     * @return ResourceSet A ResourceSet containing all the resource records.
     */
    public function get(?string $recordType = null): ResourceSet
    {
        $resourceSet = new ResourceSet($this);

        foreach ($this->resource()->getResourceRecords() as $rrset) {
            if ($recordType === null || $rrset->getType() === $recordType) {
                $resourceSet->addResource($rrset->setZone($this));
            }
        }

        return $resourceSet;
    }

    /**
     * Find all the resource records with the given $resourceRecordName in the current zone. If $recordType is
     * specified, only get those specific resource records.
     *
     * @param string      $resourceRecordName The name of the resource record.
     * @param string|null $recordType         (optional) The type of resource record.
     *
     * @return ResourceSet A ResourceSet containing all the resource records.
     */
    public function find(string $resourceRecordName, ?string $recordType = null): ResourceSet
    {
        $records = $this->get($recordType);

        $foundResources = new ResourceSet($this);

        foreach ($records as $record) {
            if (
                $record->getName() === $resourceRecordName
                || $record->getName() === sprintf('%s.%s', $resourceRecordName, $this->zone)
            ) {
                $foundResources->addResource($record);
            }
        }

        return $foundResources;
    }

    /**
     * Make (but not insert/POST) a new resource record.
     *
     * @param string $name    The resource record name.
     * @param string $type    The type of the resource record.
     * @param string $content The content of the resource record.
     * @param int    $ttl     The TTL.
     *
     * @throws Exceptions\InvalidRecordType If the given type is invalid.
     *
     * @return ResourceRecord The constructed ResourceRecord.
     */
    public function make(string $name, string $type, $content, int $ttl): ResourceRecord
    {
        return Helper::createResourceRecord($this->zone, compact('name', 'type', 'content', 'ttl'));
    }

    /**
     * Get the zone in AXFR format.
     *
     * @return string The zone in AXFR format.
     */
    public function export(): string
    {
        $result = $this->connector->get($this->getZonePath('/export'));

        return $result['zone'];
    }

    /**
     * Set an NSEC3PARAM for this zone, and save it.
     *
     * @param string|null $nsec3param The NSEC3PARAM value to set or null to unset.
     *
     * @throws InvalidNsec3Param If the hash algorithm is invalid.
     * @throws InvalidNsec3Param If the flags parameter is invalid.
     * @throws InvalidNsec3Param If the iteration parameter is invalid.
     * @throws InvalidNsec3Param If the hash salt is invalid.
     *
     * @return bool True when updated.
     */
    public function setNsec3param(?string $nsec3param): bool
    {
        $zone = $this->resource()->setNsec3param($nsec3param);
        $transformer = new Nsec3paramTransformer($zone);

        return $this->put($transformer);
    }

    /**
     * Unset the NSEC3PARAM for this zone, and save it.
     *
     * @throws InvalidNsec3Param If the given param is invalid.
     *
     * @return bool True when updated.
     */
    public function unsetNsec3param(): bool
    {
        return $this->setNsec3param(null);
    }

    /**
     * Send a DNS notify to all the slaves.
     *
     * @return bool True when the DNS notify was successfully sent
     */
    public function notify(): bool
    {
        $result = $this->connector->put($this->getZonePath('/notify'));

        /*
         * The notify PUT request will return an 200 with no body, so the $result is empty. If this is the case, the PUT was
         * successful. If there was an error, an exception will be thrown.
         */
        return empty($result);
    }

    /**
     * Enable DNSSEC for this zone.
     *
     * @return bool True when enabled.
     */
    public function enableDnssec(): bool
    {
        return $this->setDnssec(true);
    }

    /**
     * Disable DNSSEC for this zone. WARNING: this will remove ALL crypto keys for this zone!
     *
     * @return bool True when disabled.
     */
    public function disableDnssec(): bool
    {
        return $this->setDnssec(false);
    }

    /**
     * Enable or disable DNSSEC for this zone.
     *
     * @param bool $state True to enable, false to disable.
     *
     * @return bool True when the request succeeded.
     */
    public function setDnssec(bool $state): bool
    {
        return $this->put(new DnssecTransformer(['dnssec' => $state]));
    }

    /**
     * Get the DNSSEC instance for this zone.
     *
     * @return Cryptokey The DNSSEC instance.
     */
    public function dnssec(): Cryptokey
    {
        return new Cryptokey($this->connector, $this->zone);
    }
}
