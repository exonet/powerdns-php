<?php
namespace Exonet\Powerdns\tests\functional;

use Exonet\Powerdns\Resources\TSIGKey as TSIGKeyResource;
use Exonet\Powerdns\TSIGKeyAlgorithms;

/**
 * @internal
 */
class TSIGKeysTest extends FunctionalTestCase {

    public function testCreateTSIGKey(): void {

        $name = "test-key";

        $manager  = $this->powerdns->tsigkeys();
        $resource = new TSIGKeyResource();

        $resource->setName($name);
        $resource->setAlgorithm(TSIGKeyAlgorithms::HMAC_SHA512);

        $key = $manager->create($resource);

        $this->assertSame(1, $key->count());

        $created = $key->offsetGet(0);
        $this->assertNotEquals("", $created->getKey());

        // cleanup
        $manager->delete($created);

    }

    public function testCreateWithNonUrlFriendlyName(): void {
        $name = "this/is/not/aa-_412'aur\\asd-url-friendly";

        $manager  = $this->powerdns->tsigkeys();
        $resource = new TSIGKeyResource();

        $resource->setName($name);
        $resource->setAlgorithm(TSIGKeyAlgorithms::HMAC_SHA512);

        $key = $manager->create($resource);

        $this->assertSame(1, $key->count());

        $created = $key->offsetGet(0);
        $this->assertNotEquals("", $created->getKey());

        // cleanup
        $manager->delete($created);
    }

}
