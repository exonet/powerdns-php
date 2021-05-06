# Changelog

All notable changes to `powerdns-php` will be documented in this file.

Updates should follow the [Keep a CHANGELOG](http://keepachangelog.com/) principles.

## Unreleased
[Compare v3.1.0 - Unreleased](https://github.com/exonet/powerdns-php/compare/v3.1.0...develop)
### Added
- Added a method `unsetNsec3param()` to unset the nsec3param for a zone.
- Unit tests for enabling, disabling and setting DNSSEC.

### Changed
- Calling `setNsec3Param()` with value `null` will unset the `nsec3param`.

### Fixed
- Updated the stable composer.phar URL to properly run tests.

## [v3.1.0](https://github.com/exonet/powerdns-php/releases/tag/v3.1.0) - 2021-02-01
[Compare v3.0.0 - v3.1.0](https://github.com/exonet/powerdns-php/compare/v2.0.0...v3.1.0)
### Added
- The `omitDnssecAndEditedSerialFields` parameter in `listZones`. (@frankvanhest - #61)

## [v3.0.0](https://github.com/exonet/powerdns-php/releases/tag/v3.0.0) - 2021-01-05
[Compare v2.6.0 - v3.0.0](https://github.com/exonet/powerdns-php/compare/v2.6.0...v3.0.0)
### Breaking
- Dropped support for unsupported PHP versions. PHP 7.3 (or newer) is now required.

### Changed
- Updated Guzzle to 7.2. (@itspluxstahre #50)
- Updated PHPUnit to 9.5.0.
- When running tests a `composer update` will be executed for each environment to validate the packages/config for the specified PHP version.

## [v2.6.0](https://github.com/exonet/powerdns-php/releases/tag/v2.6.0) - 2020-12-21
[Compare v2.5.2 - v2.6.0](https://github.com/exonet/powerdns-php/compare/v2.5.2...v2.6.0)
### Added
- Support for PHP 8. (@akondas #54)
- Support for the PowerDNS search endpoint. (#51)
- The `notify` method on a zone. (@frankvanhest #56)

## [v2.5.2](https://github.com/exonet/powerdns-php/releases/tag/v2.5.2) - 2020-11-26
[Compare v2.5.1 - v2.5.2](https://github.com/exonet/powerdns-php/compare/v2.5.1...v2.5.2)
### Added
- The `LUA` record type. (#53)

## [v2.5.1](https://github.com/exonet/powerdns-php/releases/tag/v2.5.1) - 2020-11-06
[Compare v2.5.0 - v2.5.1](https://github.com/exonet/powerdns-php/compare/v2.5.0...v2.5.1)
### Fixed
- The `account` is send to PowerDNS when creating a zone. (#47)

## [v2.5.0](https://github.com/exonet/powerdns-php/releases/tag/v2.5.0) - 2020-10-30
[Compare v2.4.0 - v2.5.0](https://github.com/exonet/powerdns-php/compare/v2.4.0...v2.5.0)
### Added
- Get PowerDNS statistics.

## [v2.4.0](https://github.com/exonet/powerdns-php/releases/tag/v2.4.0) - 2020-08-04
[Compare v2.3.1 - v2.4.0](https://github.com/exonet/powerdns-php/compare/v2.3.1...v2.4.0)
### Added
- Export a zone in AXFR format. (#35)
- Get the zone from a ResourceRecord. (#40)
- Get the PowerDNS version of a server with `$client->serverVersion()`.
- Added a `docker-compose.yml` and `run-tests.sh` script to easy test against a combination of different PHP and PowerDNS versions.

### Changed
- Travis now uses Docker to run the tests.

### Fixed
- `$resouceRecord->getComments()` no longer throws an exception if there are no comments. (#38)

## [v2.3.1](https://github.com/exonet/powerdns-php/releases/tag/v2.3.1) - 2020-05-12
[Compare v2.3.0 - v2.3.1](https://github.com/exonet/powerdns-php/compare/v2.3.0...v2.3.1)
### Fixed
- No longer throw an exception when throwing an exception if the response body is not an error. (#32)

## [v2.3.0](https://github.com/exonet/powerdns-php/releases/tag/v2.3.0) - 2020-04-30
[Compare v2.2.0 - v2.3.0](https://github.com/exonet/powerdns-php/compare/v2.2.0...v2.3.0)
### Added
- The record type `SPF`. ([Th3Mouk](https://github.com/Th3Mouk) - [#30](https://github.com/exonet/powerdns-php/pull/30))

### Fixed
- Removed record types `APL`, `HIP` and `TA` which are not supported by PowerDNS.

## [v2.2.0](https://github.com/exonet/powerdns-php/releases/tag/v2.2.0) - 2020-04-01
[Compare v2.1.0 - v2.2.0](https://github.com/exonet/powerdns-php/compare/v2.1.0...v2.2.0)
### Added
- Create a new zone based on a zone resource object.

## [v2.1.0](https://github.com/exonet/powerdns-php/releases/tag/v2.1.0) - 2020-03-11
[Compare v2.0.0 - v2.1.0](https://github.com/exonet/powerdns-php/compare/v2.0.0...v2.1.0)
### Added
- It is now possible to set the NSEC3PARAM on a zone. ([mkevenaar-docktera](https://github.com/mkevenaar-docktera) - #25 #26)

### Changed
- The validation of the NSEC3PARAM is improved. (#27)

## [v2.0.0](https://github.com/exonet/powerdns-php/releases/tag/v2.0.0) - 2020-01-29
[Compare v1.1.0 - v2.0.0](https://github.com/exonet/powerdns-php/compare/v1.1.0...v2.0.0)
### Breaking
- Renamed `SOA-EDIT-API` to `SOA-EDIT` when creating a new zone.
- Implemented new `SOA-EDIT-API` logic when creating a new zone that defaults to `DEFAULT` so the `SOA-EDIT` value will be used.

This change will break your SOA increment if not configured correctly in the zone meta data. You need to update the zone
meta yourself in whatever backend you use for PowerDNS. See the following quote from the [PowerDNS website (section API)](https://doc.powerdns.com/md/authoritative/upgrading/):

> Incompatible change: SOA-EDIT-API now follows SOA-EDIT-DNSUPDATE instead of SOA-EDIT (incl. the fact that it now has
> a default value of DEFAULT). You must update your existing SOA-EDIT-API metadata (set SOA-EDIT to your previous
> SOA-EDIT-API value, and SOA-EDIT-API to SOA-EDIT to keep the old behaviour).

### Added
- PowerDNS 4.2 support (see 'breaking' above).
- PHP 7.4 support
- Functional tests for SOA increments.

## [v1.1.0](https://github.com/exonet/powerdns-php/releases/tag/v1.1.0) - 2019-10-21
[Compare v1.0.1 - v1.1.0](https://github.com/exonet/powerdns-php/compare/v1.0.1...v1.1.0)
### Added
- `$powerdns->listZones: Zone[];` to get all zones. ([jackdpeterson](https://github.com/jackdpeterson) - #14)
- `$zone->getCanonicalName(): string;` to get the canonical zone name. ([jackdpeterson](https://github.com/jackdpeterson) - #14)

## [v1.0.1](https://github.com/exonet/powerdns-php/releases/tag/v1.0.1) - 2019-07-17
[Compare v1.0.0 - v1.0.1](https://github.com/exonet/powerdns-php/compare/v1.0.0...v1.0.1)
### Changed
- The `Content-Type` header is now also set when making calls to the API. ([#11](https://github.com/exonet/powerdns-php/issues/11))

### Fixed
- Changed the payload key from `set_ptr` to the correct `set-ptr` in the RRSetTransformer. ([#12](https://github.com/exonet/powerdns-php/issues/12))

## [v1.0.0](https://github.com/exonet/powerdns-php/releases/tag/v1.0.0) - 2019-07-05
[Compare v0.2.4 - v1.0.0](https://github.com/exonet/powerdns-php/compare/v0.2.4...v1.0.0)
### Changed
- Public release with quick-start example in readme.

## [v0.2.4](https://github.com/exonet/powerdns-php/releases/tag/v0.2.4) - 2019-06-18
[Compare v0.2.3 - v0.2.3](https://github.com/exonet/powerdns-php/compare/v0.2.3...v0.2.4)
### Changed
- Renamed the `toggleDnssec` method to `setDnssec`.

## [v0.2.3](https://github.com/exonet/powerdns-php/releases/tag/v0.2.3) - 2019-04-10
[Compare v0.2.2 - v0.2.3](https://github.com/exonet/powerdns-php/compare/v0.2.2...v0.2.3)
### Removed
- The alias functionality that replaces the `@` for the full domain name in the content section when adding DNS records.

## [v0.2.2](https://github.com/exonet/powerdns-php/releases/tag/v0.2.2) - 2019-03-18
[Compare v0.2.1 - v0.2.2](https://github.com/exonet/powerdns-php/compare/v0.2.1...v0.2.2)
### Added
- It is now possible to enable/disable DNSSEC directly on a zone.

## [v0.2.1](https://github.com/exonet/powerdns-php/releases/tag/v0.2.1) - 2019-03-12
[Compare v0.2.0 - v0.2.1](https://github.com/exonet/powerdns-php/compare/v0.2.0...v0.2.1)
### Fixed
- DS records in the Cryptokey resource can be `null`.

## [v0.2.0](https://github.com/exonet/powerdns-php/releases/tag/v0.2.0) - 2019-03-04
### Added
- Initial public release.
