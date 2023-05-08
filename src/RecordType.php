<?php

namespace Exonet\Powerdns;

/**
 * List of possible DNS Record types supported by PowerDNS.
 * Source: https://doc.powerdns.com/md/types/.
 */
class RecordType
{
    /**
     * Address record.
     *
     * Returns a 32-bit IPv4 address, most commonly used to map hostnames to an IP address of the host, but it is also
     * used for DNSBLs, storing subnet masks in RFC 1101, etc.
     */
    public const A = 'A';

    /**
     * IPv6 address record.
     *
     * Returns a 128-bit IPv6 address, most commonly used to map hostnames to an IP address of the host.
     */
    public const AAAA = 'AAAA';

    /**
     * AFS database record.
     *
     * Location of database servers of an AFS cell. This record is commonly used by AFS clients to contact AFS cells
     * outside their local domain. A subtype of this record is used by the obsolete DCE/DFS file system.
     */
    public const AFSDB = 'AFSDB';

    /**
     * Record similar to CNAME, specially designed to pointing services in a domain. It allows moving services between
     * servers, and can define group of hosts serves any service, and moving service between hosts.
     */
    public const ALIAS = 'ALIAS';

    /**
     * Certification Authority Authorization.
     *
     * DNS Certification Authority Authorization, constraining acceptable CAs for a host/domain
     */
    public const CAA = 'CAA';

    /**
     * Child copy of DNSKEY record, for transfer to parent.
     */
    public const CDNSKEY = 'CDNSKEY';

    /**
     * Child DS.
     *
     * Child copy of DS record, for transfer to parent.
     */
    public const CDS = 'CDS';

    /**
     * Certificate record.
     *
     * Stores PKIX, SPKI, PGP, etc.
     */
    public const CERT = 'CERT';

    /**
     * Canonical name record.
     *
     * Alias of one name to another: the DNS lookup will continue by retrying the lookup with the new name.
     */
    public const CNAME = 'CNAME';

    /**
     * DHCP identifier.
     *
     * Used in conjunction with the FQDN option to DHCP.
     */
    public const DHCID = 'DHCID';

    /**
     * DNSSEC Lookaside Validation record.
     *
     * For publishing DNSSEC trust anchors outside of the DNS delegation chain. Uses the same format as the DS record.
     * RFC 5074 describes a way of using these records.
     */
    public const DLV = 'DLV';

    /**
     * Alias for a name and all its subnames, unlike CNAME, which is an alias for only the exact name. Like a CNAME
     * record, the DNS lookup will continue by retrying the lookup with the new name.
     */
    public const DNAME = 'DNAME';

    /**
     * DNS Key record.
     *
     * The key record used in DNSSEC. Uses the same format as the KEY record.
     */
    public const DNSKEY = 'DNSKEY';

    /**
     * Delegation signer.
     *
     * The record used to identify the DNSSEC signing key of a delegated zone
     */
    public const DS = 'DS';

    /**
     * IPsec Key.
     *
     * Key record that can be used with IPsec.
     */
    public const IPSECKEY = 'IPSECKEY';

    /**
     * Key record.
     *
     * Used only for SIG(0) (RFC 2931) and TKEY (RFC 2930). RFC 3445 eliminated their use for application keys and
     * limited their use to DNSSEC. RFC 3755 designates DNSKEY as the replacement within DNSSEC. RFC 4025
     * designates IPSECKEY as the replacement for use with IPsec.
     */
    public const KEY = 'KEY';

    /**
     * Key Exchanger record.
     *
     * Used with some cryptographic systems (not including DNSSEC) to identify a key management agent for the
     * associated domain-name. Note that this has nothing to do with DNS Security. It is Informational status, rather
     * than being on the IETF standards-track. It has always had limited deployment, but is still in use.
     */
    public const KX = 'KX';

    /**
     * Location record.
     *
     * Specifies a geographical location associated with a domain name.
     */
    public const LOC = 'LOC';

    /**
     * PowerDNS specific record.
     *
     * These records contain small snippets of configuration that enable dynamic behaviour based on
     * requester IP address, requester’s EDNS Client Subnet, server availability or other factors.
     * https://doc.powerdns.com/authoritative/lua-records/index.html
     */
    public const LUA = 'LUA';

    /**
     * Mail exchange record.
     *
     * Maps a domain name to a list of message transfer agents for that domain.
     */
    public const MX = 'MX';

    /**
     * Naming Authority Pointer.
     *
     * Allows regular-expression-based rewriting of domain names which can then be used as URIs, further domain names
     * to lookups, etc.
     */
    public const NAPTR = 'NAPTR';

    /**
     * Name server record.
     *
     * Delegates a DNS zone to use the given authoritative name servers.
     */
    public const NS = 'NS';

    /**
     * Next Secure record.
     *
     * Part of DNSSEC—used to prove a name does not exist. Uses the same format as the (obsolete) NXT record.
     */
    public const NSEC = 'NSEC';

    /**
     * Next Secure record version 3.
     *
     * An extension to DNSSEC that allows proof of nonexistence for a name without permitting zonewalking.
     */
    public const NSEC3 = 'NSEC3';

    /**
     * NSEC3 parameters.
     *
     * Parameter record for use with NSEC3.
     */
    public const NSEC3PARAM = 'NSEC3PARAM';

    /**
     * OpenPGP public key record.
     *
     * A DNS-based Authentication of Named Entities (DANE) method for publishing and locating OpenPGP public keys in
     * DNS for a specific email address using an OPENPGPKEY DNS resource record.
     */
    public const OPENPGPKEY = 'OPENPGPKEY';

    /**
     * Pointer record.
     *
     * Pointer to a canonical name. Unlike a CNAME, DNS processing stops and just the name is returned. The most common
     * use is for implementing reverse DNS lookups, but other uses include such things as DNS-SD.
     */
    public const PTR = 'PTR';

    /**
     * Responsible Person.
     *
     * Information about the responsible person(s) for the domain. Usually an email address with the @ replaced by a .
     */
    public const RP = 'RP';

    /**
     * DNSSEC signature.
     *
     * Signature for a DNSSEC-secured record set. Uses the same format as the SIG record.
     */
    public const RRSIG = 'RRSIG';

    /**
     * Signature.
     *
     * Signature record used in SIG(0) (RFC 2931) and TKEY (RFC 2930). RFC 3755 designated RRSIG as the replacement
     * for SIG for use within DNSSEC.
     */
    public const SIG = 'SIG';

    /**
     * Start of [a zone of] authority record.
     *
     * Specifies authoritative information about a DNS zone, including the primary name server, the email of the domain
     * administrator, the domain serial number, and several timers relating to refreshing the zone.
     */
    public const SOA = 'SOA';

    /**
     * Sender Policy Framework.
     *
     * Specified as part of the SPF protocol as an alternative to of storing SPF data in TXT records. Uses the same
     * format as the earlier TXT record.
     */
    public const SPF = 'SPF';

    /**
     * Service locator.
     *
     * Generalized service location record, used for newer protocols instead of creating protocol-specific records such
     * as MX.
     */
    public const SRV = 'SRV';

    /**
     * SSH Public Key Fingerprint.
     *
     * Resource record for publishing SSH public host key fingerprints in the DNS System, in order to aid in verifying
     * the authenticity of the host. RFC 6594 defines ECC SSH keys and SHA-256 hashes. See the IANA SSHFP RR parameters
     * registry for details.
     */
    public const SSHFP = 'SSHFP';

    /**
     * Transaction Key record.
     *
     * A method of providing keying material to be used with TSIG that is encrypted under the public key in an
     * accompanying KEY RR.
     */
    public const TKEY = 'TKEY';

    /**
     * TLSA certificate association.
     *
     * A record for DANE. RFC 6698 defines "The TLSA DNS resource record is used to associate a TLS server certificate
     * or public key with the domain name where the record is found, thus forming a 'TLSA certificate association'".
     */
    public const TLSA = 'TLSA';

    /**
     * Transaction Signature.
     *
     * Can be used to authenticate dynamic updates as coming from an approved client, or to authenticate responses as
     * coming from an approved recursive name server similar to DNSSEC.
     */
    public const TSIG = 'TSIG';

    /**
     * Text record.
     *
     * Originally for arbitrary human-readable text in a DNS record. Since the early 1990s, however, this record more
     * often carries machine-readable data, such as specified by RFC 1464, opportunistic encryption, Sender Policy
     * Framework, DKIM, DMARC, DNS-SD, etc.
     */
    public const TXT = 'TXT';

    /**
     * Uniform Resource Identifier.
     *
     * Can be used for publishing mappings from hostnames to URIs.
     */
    public const URI = 'URI';

    /**
     * Prefix for "Unknown" type records. Private resource records fall under this category.
     * https://datatracker.ietf.org/doc/html/rfc3597#section-5
     */
    public const unknownTypePrefix = 'TYPE';

    /**
     * Minimal type number for a Private resource record.
     * https://www.iana.org/assignments/dns-parameters/dns-parameters.xhtml#dns-parameters-4
     */
    public const privateTypeMin = 65280;

    /**
     * Maximum type number for a Private resource record.
     * https://www.iana.org/assignments/dns-parameters/dns-parameters.xhtml#dns-parameters-4
     */
    public const privateTypeMax = 65534;
}
