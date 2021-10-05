<?php

namespace Exonet\Powerdns\Resources;

class SearchResult
{
    /**
     * @var string|null The content.
     */
    private $content;

    /**
     * @var bool|null True when disabled.
     */
    private $disabled;

    /**
     * @var string The name.
     */
    private $name;

    /**
     * @var string The result object type. Can be 'record', 'zone' or 'comment'.
     */
    private $objectType;

    /**
     * @var string The zone ID.
     */
    private $zoneId;

    /**
     * @var string|null The zone.
     */
    private $zone;

    /**
     * @var string|null The record type.
     */
    private $type;

    /**
     * @var int|null The TTL value.
     */
    private $ttl;

    /**
     * SearchResult constructor.
     *
     * @param array|null $result PowerDNS API result array.
     */
    public function __construct(?array $result = null)
    {
        if ($result) {
            $this->setFromApiResponse($result);
        }
    }

    /**
     * Get the content.
     *
     * @return string|null The content.
     */
    public function getContent(): ?string
    {
        return $this->content;
    }

    /**
     * Get the disabled state.
     *
     * @return bool|null True when disabled.
     */
    public function isDisabled(): ?bool
    {
        return $this->disabled;
    }

    /**
     * Get the name.
     *
     * @return string The name.
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * Get the object type. Can be 'record', 'zone' or 'comment'.
     *
     * @return string The object type.
     */
    public function getObjectType(): string
    {
        return $this->objectType;
    }

    /**
     * Get the zone ID.
     *
     * @return string The zone ID.
     */
    public function getZoneId(): string
    {
        return $this->zoneId;
    }

    /**
     * Get the zone name.
     *
     * @return string|null The zone.
     */
    public function getZone(): ?string
    {
        return $this->zone;
    }

    /**
     * Get the record type.
     *
     * @return string|null The record type.
     */
    public function getType(): ?string
    {
        return $this->type;
    }

    /**
     * Get the record Time To Live (TTL).
     *
     * @return int|null The TTL.
     */
    public function getTtl(): ?int
    {
        return $this->ttl;
    }

    /**
     * Fill this class based on the data from PowerDNS.
     *
     * @param array $data The data as returned from PowerDNS.
     *
     * @return $this The current SearchResult instance.
     */
    public function setFromApiResponse(array $data): self
    {
        $this->content = $data['content'] ?? null;
        $this->disabled = isset($data['disabled']) ? (bool) $data['disabled'] : null;
        $this->name = $data['name'];
        $this->objectType = $data['object_type'];
        $this->ttl = isset($data['ttl']) ? (int) $data['ttl'] : null;
        $this->type = $data['type'] ?? null;
        $this->zone = $data['zone'] ?? null;
        $this->zoneId = $data['zone_id'];

        return $this;
    }
}
