<?php

declare(strict_types=1);

namespace Exonet\Powerdns\Resources;

use Exonet\Powerdns\Exceptions\InvalidKindType;
use Exonet\Powerdns\Exceptions\InvalidNsec3Param;
use Exonet\Powerdns\Exceptions\InvalidSoaEditType;

class Zone
{
    /**
     * @var string Name of the zone (e.g. "example.com.") MUST have a trailing dot.
     */
    private $name;

    /**
     * @var string Zone kind, one of "Native", "Master", "Slave".
     */
    private $kind = 'Native';

    /**
     * @var int The SOA serial number.
     */
    private $serial;

    /**
     * @var int The SOA serial notifications have been sent out for.
     */
    private $notifiedSerial;

    /**
     * @var string[] List of IP addresses configured as a master for this zone ("Slave" type zones only).
     */
    private $masters = [];

    /**
     * @var bool Whether or not this zone is DNSSEC signed (inferred from presigned being true XOR presence of at least
     *           one cryptokey with active being true).
     */
    private $dnssec = false;

    /**
     * @var string The NSEC3PARAM record used for signing cryptokeys.
     */
    private $nsec3param;

    /**
     * @var string The SOA-EDIT-API metadata item.
     */
    private $soaEditApi = 'INCEPTION-INCREMENT';

    /**
     * @var bool Whether or not the zone will be rectified on data changes via the API.
     */
    private $apiRectify = true;

    /**
     * @var string The account created this zone. MAY be set. Its value is defined by local policy.
     */
    private $account;

    /**
     * @var string[] Array containing the nameserver for this zone. Required when creating a new zone, will not be
     *               returned by the server.
     */
    private $nameservers = [];

    /**
     * Set the zone data based on the API response.
     *
     * @param mixed[] $data The API response.
     *
     * @return Zone The current zone instance.
     */
    public function setApiResponse(array $data) : self
    {
        $this->name = $data['name'];
        $this->kind = $data['kind'];
        $this->serial = $data['serial'];
        $this->notifiedSerial = $data['notified_serial'];
        $this->masters = $data['masters'];
        $this->dnssec = $data['dnssec'];
        $this->nsec3param = !empty($data['nsec3param']) ? $data['nsec3param'] : null;
        $this->soaEditApi = !empty($data['soa_edit_api']) ? $data['soa_edit_api'] : null;
        $this->apiRectify = $data['api_rectify'];
        $this->account = !empty($data['account']) ? $data['account'] : null;

        // Try setting the nameservers.
        if (isset($data['rrsets'])) {
            $nameServers = [];
            foreach ($data['rrsets'] as $resourceSet) {
                if ($resourceSet['type'] === 'NS') {
                    foreach ($resourceSet['records'] as $record) {
                        $nameServers[] = $record['content'];
                    }
                }
            }

            $this->setNameservers($nameServers);
        }

        return $this;
    }

    /**
     * Get the zone name.
     *
     * @return string The zone name.
     */
    public function getName() : string
    {
        return $this->name;
    }

    /**
     * Get the zone kind, one of "Native", "Master", "Slave".
     *
     * @return string The zone kind.
     */
    public function getKind() : string
    {
        return $this->kind;
    }

    /**
     * Get the SOA serial number.
     *
     * @return int The SOA serial number.
     */
    public function getSerial() : int
    {
        return $this->serial;
    }

    /**
     * Get the SOA serial notifications have been sent out for.
     *
     * @return int The SOA serial notifications have been sent out for.
     */
    public function getNotifiedSerial() : int
    {
        return $this->notifiedSerial;
    }

    /**
     * List of IP addresses configured as a master for this zone ("Slave" type zones only).
     *
     * @return string[] List of IP addresses.
     */
    public function getMasters() : array
    {
        return $this->masters;
    }

    /**
     * Whether or not this zone is DNSSEC signed.
     *
     * @return bool Whether or not this zone is DNSSEC signed.
     */
    public function hasDnssec() : bool
    {
        return $this->dnssec;
    }

    /**
     * Get the NSEC3PARAM for this zone.
     *
     * @return string|null The NSEC3PARAM value or null.
     */
    public function getNsec3param() : ?string
    {
        return $this->nsec3param;
    }

    /**
     * Get the SOA-EDIT-API metadata item.
     *
     * @return string|null The SOA-EDIT-API metadata item or null
     */
    public function getSoaEditApi() : ?string
    {
        return $this->soaEditApi;
    }

    /**
     * Whether or not this zone is automatically rectified by the API.
     *
     * @return bool Whether or not this zone is automatically rectified by the API.
     */
    public function hasAutoRectify() : bool
    {
        return $this->apiRectify;
    }

    /**
     * Get an array containing the nameserver for this zone. Required when creating a new zone, will not be returned by
     * the server.
     *
     * @return string[] The nameservers.
     */
    public function getNameservers() : array
    {
        return $this->nameservers;
    }

    /**
     * Get the account created this zone. MAY be set. Its value is defined by local policy.
     *
     * @return string|null The account name or null.
     */
    public function getAccount() : ?string
    {
        return $this->account;
    }

    /**
     * Name of the zone (e.g. "example.com.") MUST have a trailing dot.
     *
     * @param string $name The name of the zone.
     *
     * @return Zone The current Zone instance.
     */
    public function setName(string $name) : self
    {
        $this->name = $name;

        return $this;
    }

    /**
     * The zone kind, one of "Native", "Master", "Slave".
     *
     * @param string $kind The name of the zone.
     *
     * @throws InvalidKindType If a kind is given that is not allowed.
     *
     * @return Zone The current Zone instance.
     */
    public function setKind(string $kind) : self
    {
        $kind = ucfirst($kind);
        $allowed = ['Native', 'Master', 'Slave'];

        if (!in_array($kind, $allowed, true)) {
            throw new InvalidKindType(sprintf('Kind must be either %s. (%s given)', implode($allowed, ', '), $kind));
        }

        $this->kind = $kind;

        return $this;
    }

    /**
     * Set the SOA serial number.
     *
     * @param int $serial The new SOA serial number.
     *
     * @return Zone The current Zone instance.
     */
    public function setSerial(int $serial) : self
    {
        $this->serial = $serial;

        return $this;
    }

    /**
     * Set the SOA serial notification.
     *
     * @param int $notifiedSerial The new SOA serial notification.
     *
     * @return Zone The current Zone instance.
     */
    public function setNotifiedSerial(int $notifiedSerial) : self
    {
        $this->notifiedSerial = $notifiedSerial;

        return $this;
    }

    /**
     * Set the list of IP addresses configured as a master for this zone ("Slave" kind zones only).
     *
     * @param string[] $masters The list of IP addresses to set as master.
     *
     * @throws InvalidKindType If the kind is not set as Slave.
     *
     * @return Zone The current Zone instance.
     */
    public function setMasters(array $masters) : self
    {
        if ($this->kind !== 'Slave') {
            throw new InvalidKindType(sprintf('Only "Slave" kind zones can set masters, not "%".', $this->kind));
        }

        $this->masters = $masters;

        return $this;
    }

    /**
     * Set DNSSEC to true or false.
     *
     * @param bool $dnssec The new DNSSEC value
     *
     * @return Zone The current Zone instance.
     */
    public function setDnssec(bool $dnssec) : self
    {
        $this->dnssec = $dnssec;

        return $this;
    }

    /**
     * Set the NSEC3PARAM for this zone.
     *
     * @param string $nsec3param The NSEC3PARAM value to set.
     *
     * @throws InvalidNsec3Param If the hash algorithm is invalid.
     * @throws InvalidNsec3Param If the flags parameter is invalid.
     * @throws InvalidNsec3Param If the iteration parameter is invalid.
     * @throws InvalidNsec3Param If the hash salt is invalid.
     *
     * @return Zone The current Zone instance.
     */
    public function setNsec3param(string $nsec3param) : self
    {
        // Validate the nsec3param.
        list($algorithm, $flags, $iterations, $salt) = explode(' ', $nsec3param);

        if ((int) $algorithm !== 1) {
            throw new InvalidNsec3Param('The nsec3param hash algorithm parameter must be set to 1.');
        }
        if ((int) $flags !== 0) {
            throw new InvalidNsec3Param('The nsec3param flags parameter must be set to 0.');
        }
        if ($iterations === 0 || $iterations > 2500) {
            throw new InvalidNsec3Param('The nsec3param iterations parameter must be between 0 and 2500.');
        }
        if (strlen($salt) === 0 || strlen($salt) > 255) {
            throw new InvalidNsec3Param('The nsec3param hash salt length must be between 0 and 255 characters.');
        }

        $this->nsec3param = $nsec3param;

        return $this;
    }

    /**
     * The SOA edit API kind, one of "INCREMENT-WEEKS", "INCEPTION-EPOCH", "INCEPTION-INCREMENT", "EPOCH" or "NONE".
     *
     * @param string $soaEditApi The SOA edit API value.
     *
     * @throws InvalidSoaEditType If a kind is given that is not allowed.
     *
     * @return Zone The current Zone instance.
     */
    public function setSoaEditApi(string $soaEditApi) : self
    {
        $soaEditApi = strtoupper($soaEditApi);
        $allowed = ['INCREMENT-WEEKS', 'INCEPTION-EPOCH', 'INCEPTION-INCREMENT', 'EPOCH', 'NONE'];

        if (!in_array($soaEditApi, $allowed, true)) {
            throw new InvalidSoaEditType(
                sprintf('Kind must be either %s. (%s given)', implode($allowed, ', '), $soaEditApi)
            );
        }

        $this->soaEditApi = $soaEditApi;

        return $this;
    }

    /**
     * Set API-RECTIFY to true or false.
     *
     * @param bool $apiRectify The new API rectify value
     *
     * @return Zone The current Zone instance.
     */
    public function setApiRectify(bool $apiRectify) : self
    {
        $this->apiRectify = $apiRectify;

        return $this;
    }

    /**
     * Set an array containing the nameserver for this zone. Required when creating a new zone, will not be returned by
     * the server.
     *
     * @param string[] $nameservers Array with nameservers.
     *
     * @return Zone The current Zone instance.
     */
    public function setNameservers(array $nameservers) : self
    {
        $this->nameservers = $nameservers;

        return $this;
    }

    /**
     * Account of the zone.
     *
     * @param string $account The account of the zone.
     *
     * @return Zone The current Zone instance.
     */
    public function setAccount(string $account) : self
    {
        $this->account = $account;

        return $this;
    }
}
