# Changelog

All notable changes to `powerdns-php` will be documented in this file.

Updates should follow the [Keep a CHANGELOG](http://keepachangelog.com/) principles.

## Unreleased
[Compare v1.0.1 - Unreleased](https://github.com/exonet/powerdns-php/compare/v1.0.1...develop)

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
