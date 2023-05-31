<?php

namespace Exonet\Powerdns;

/**
 * List of possible DNS meta types supported by PowerDNS.
 * Source: https://doc.powerdns.com/authoritative/domainmetadata.html
 */
class MetaType
{
    /**
     * The following options can only be read (not written to) via the HTTP API metadata endpoint.
     */
    public const READ_ONLY = [
        self::API_RECTIFY,
        self::AXFR_MASTER_TSIG,
        self::LUA_AXFR_SCRIPT,
        self::NSEC3NARROW,
        self::NSEC3PARAM,
        self::PRESIGNED,
        self::TSIG_ALLOW_AXFR,
        self::SOA_EDIT_API,
    ];

    /**
     * Per-zone AXFR ACLs can be stored in the domainmetadata table.
     */
    public const ALLOW_AXFR_FROM = 'ALLOW-AXFR-FROM';

    /**
     * This metadata item controls whether a zone is fully rectified on changes to the contents of a zone made through
     * the API.
     *
     * @since PowerDNS 4.1.0
     */
    public const API_RECTIFY = 'API-RECTIFY';

    /**
     * The IP address to use as a source address for sending AXFR and IXFR requests.
     */
    public const AXFR_SOURCE = 'AXFR-SOURCE';

    /**
     * A list of IP ranges that are allowed to perform updates on any domain.
     */
    public const ALLOW_DNSUPDATE_FROM = 'ALLOW-DNSUPDATE-FROM';

    /**
     * This setting allows you to set the TSIG key required to do an DNS update. If you have GSS-TSIG enabled, you can
     * use Kerberos principals here.
     */
    public const TSIG_ALLOW_DNSUPDATE = 'TSIG-ALLOW-DNSUPDATE';

    /**
     * Tell PowerDNS to forward to the master server if the zone is configured as slave. Masters are determined by the
     * masters field in the domains table.
     */
    public const FORWARD_DNSUPDATE = 'FORWARD-DNSUPDATE';

    /**
     * This configures how the soa serial should be updated.
     */
    public const SOA_EDIT_DNSUPDATE = 'SOA-EDIT-DNSUPDATE';

    /**
     * Send a notification to all slave servers after every update.
     */
    public const NOTIFY_DNSUPDATE = 'NOTIFY-DNSUPDATE';

    /**
     * When notifying this domain, also notify this nameserver (can occur multiple times).
     */
    public const ALSO_NOTIFY = 'ALSO-NOTIFY';

    /**
     * Use this named TSIG key to retrieve this zone from its master.
     */
    public const AXFR_MASTER_TSIG = 'AXFR-MASTER-TSIG';

    /**
     * Allow this GSS principal to perform AXFR retrieval.
     *
     * @removed PowerDNS 4.3.1
     * @since PowerDNS 4.7.0
     */
    public const GSS_ALLOW_AXFR_PRINCIPAL = 'GSS-ALLOW-AXFR-PRINCIPAL';

    /**
     * Use this principal for accepting GSS context.
     */
    public const GSS_ACCEPTOR_PRINCIPAL = 'GSS-ACCEPTOR-PRINCIPAL';

    /**
     * If set to 1, attempt IXFR when retrieving zone updates. Otherwise IXFR is not attempted.
     */
    public const IXFR = 'IXFR';

    /**
     * Script to be used to edit incoming AXFRs. This value will override the lua-axfr-script setting. Use 'NONE' to
     * remove a global script.
     */
    public const LUA_AXFR_SCRIPT = 'LUA-AXFR-SCRIPT';

    /**
     * Set to "1" to tell PowerDNS this zone operates in NSEC3 'narrow' mode.
     */
    public const NSEC3NARROW = 'NSEC3NARROW';

    /**
     * NSEC3 parameters of a DNSSEC zone. Will be used to synthesize the NSEC3PARAM record. If present, NSEC3 is used,
     * if not present, zones default to NSEC.
     */
    public const NSEC3PARAM = 'NSEC3PARAM';

    /**
     * This zone carries DNSSEC RRSIGs (signatures), and is presigned. PowerDNS sets this flag automatically upon
     * incoming zone transfers (AXFR) if it detects DNSSEC records in the zone. However, if you import a presigned zone
     * using zone2sql or pdnsutil load-zone you must explicitly set the zone to be PRESIGNED. Note that PowerDNS will
     * not be able to correctly serve the zone if the imported data is bogus or incomplete.
     *
     * If a zone is presigned, the content of the metadata must be "1" (without the quotes). Any other value will not
     * signal presignedness.
     */
    public const PRESIGNED = 'PRESIGNED';

    /**
     * Whether to publish CDNSKEY and/or CDS records as defined in RFC 7344.
     * To publish CDNSKEY records of the KSKs for the zone, set PUBLISH-CDNSKEY to 1.
     * To publish CDS records for the KSKs in the zone, set PUBLISH-CDS to a comma- separated list of signature
     * algorithm numbers.
     */
    public const PUBLISH_CDNSKEY = 'PUBLISH-CDNSKEY';
    public const PUBLISH_CDS = 'PUBLISH-CDS';

    /**
     * If set to 1, will make PowerDNS renotify the slaves after an AXFR is received from a master. Any other value
     * means that no renotifies are done. If not set at all, action will depend on the slave-renotify setting.
     *
     * @since PowerDNS 4.3.0
     */
    public const SLAVE_RENOTIFY = 'SLAVE-RENOTIFY';

    /**
     * When serving this zone, modify the SOA serial number in one of several ways. Mostly useful to get slaves to
     * re-transfer a zone regularly to get fresh RRSIGs.
     */
    public const SOA_EDIT = 'SOA-EDIT';

    /**
     * On changes to the contents of a zone made through the API, the SOA record will be edited according to the
     * SOA-EDIT-API rules. These rules are the same as the SOA-EDIT-DNSUPDATE rules. If not set during zone creation,
     * a SOA-EDIT-API metadata record is created and set to DEFAULT. If this record is removed from the backend,
     * the default behavior is to not do any SOA editing based on this setting. This is different from setting DEFAULT.
     */
    public const SOA_EDIT_API = 'SOA-EDIT-API';

    /**
     * Allow these named TSIG keys to AXFR this zone.
     */
    public const TSIG_ALLOW_AXFR = 'TSIG-ALLOW-AXFR';
}
