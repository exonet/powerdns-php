[![Latest Version on Packagist][ico-version]][link-packagist]
[![Software License][ico-license]](LICENSE.md)
[![Build Status][ico-travis]][link-travis]
[![Total Downloads][ico-downloads]][link-downloads]

# powerdns-php
A PHP client to communicate with the PowerDNS API.

## Install
Via Composer

```bash
$ composer require exonet/powerdns-php
```

## Usage
Basic example how to create a new DNS zone and insert a few DNS records.
```php
use Exonet\Powerdns\Powerdns;
use Exonet\Powerdns\RecordType;

// Initialize the Powerdns client.
$powerdns = new Powerdns('127.0.0.1', 'powerdns_secret_string');

// Create a new zone.
$zone = $powerdns->createZone(
    'example.com',
    ['ns1.example.com.', 'ns2.example.com.']
);

// Add two DNS records to the zone.
$zone->create([
    ['type' => RecordType::A, 'content' => '127.0.0.1', 'ttl' => 60, 'name' => '@'],
    ['type' => RecordType::A, 'content' => '127.0.0.1', 'ttl' => 60, 'name' => 'www'],
]);
```

See the [examples](examples) directory for more.

## Change log
Please see [releases][link-releases] for more information on what has changed recently.

## Testing
Testing against multiple PHP / PowerDNS versions can be done by using the provided `docker-compose.yml` and the
`run-tests.sh` shell script:

``` bash
$ docker-compose up -d
$ ./run-tests.sh
```

After running `docker-compose up -d` wait a few seconds so PowerDNS can be initialized. You can leave the containers
running and call the test script multiple times.

To test against a specific PHP / PowerDNS combination, you can provide the PHP version as first and the PowerDNS
version as second parameter:

```bash
$ ./run-tests.sh 7.4 4.3
```

## Contributing
Please see [CONTRIBUTING](.github/CONTRIBUTING.md) and [CODE_OF_CONDUCT](.github/CODE_OF_CONDUCT.md) for details.

## Security
If you discover any security related issues, please email development@exonet.nl instead of using the issue tracker.

## Credits
- [Exonet][link-author]
- [All Contributors][link-contributors]

## License
The MIT License (MIT). Please see [License File](LICENSE.md) for more information.

[ico-version]: https://img.shields.io/packagist/v/exonet/powerdns-php.svg?style=flat-square
[ico-license]: https://img.shields.io/badge/license-MIT-brightgreen.svg?style=flat-square
[ico-travis]: https://img.shields.io/travis/com/exonet/powerdns-php/master.svg?style=flat-square
[ico-downloads]: https://img.shields.io/packagist/dt/exonet/powerdns-php.svg?style=flat-square

[link-packagist]: https://packagist.org/packages/exonet/powerdns-php
[link-travis]: https://app.travis-ci.com/github/exonet/powerdns-php
[link-downloads]: https://packagist.org/packages/exonet/powerdns-php
[link-author]: https://github.com/exonet
[link-contributors]: ../../contributors
[link-releases]: https://github.com/exonet/powerdns-php/releases
