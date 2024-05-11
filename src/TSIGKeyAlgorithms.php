<?php
namespace Exonet\Powerdns;

/**
 * static file of available hashing algos
 *
 * @see https://doc.powerdns.com/authoritative/tsig.html
 */
class TSIGKeyAlgorithms {


    public const HMAC_MD5    = 'hmac-md5';
    public const HMAC_SHA1   = 'hmac-sha1';
    public const HMAC_SHA224 = "hmac-sha224";
    public const HMAC_SHA256 = "hmac-sha256";
    public const HMAC_SHA384 = "hmac-sha384";
    public const HMAC_SHA512 = "hmac-sha512";

}
