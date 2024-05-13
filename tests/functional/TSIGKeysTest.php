<?php

namespace Exonet\Powerdns\tests\functional;

use Exonet\Powerdns\Resources\TSIGKey as TSIGKeyResource;
use Exonet\Powerdns\TSIGKeyAlgorithms;

/**
 * @internal
 */
class TSIGKeysTest extends FunctionalTestCase {
    /**
     * test that normal creation of a default key works, asserting that the key is not empty
     *
     * @return void
     */
    public function testCreateTSIGKey(): void {
        $name = 'tsigkey-' . mt_rand(100, 10000);

        $manager  = $this->powerdns->tsigkeys();
        $resource = new TSIGKeyResource();

        $resource->setName($name);
        $resource->setAlgorithm(TSIGKeyAlgorithms::HMAC_SHA512);

        $key = $manager->create($resource);

        $this->assertNotEquals('', $key->getKey());
        $this->assertEquals($key->getName(), $name);

        // cleanup
        $manager->delete($key);
    }

    /**
     * testing the the single endpoint works
     *
     * @return void
     */
    public function testGetSingle(): void {
        $name = 'test-key2-' . mt_rand(100, 10000) . microtime(false);

        $manager  = $this->powerdns->tsigkeys();
        $resource = new TSIGKeyResource();

        $resource->setName($name);
        $resource->setAlgorithm(TSIGKeyAlgorithms::HMAC_SHA512);

        $key = $manager->create($resource);

        // get single key
        $fromApi = $manager->get($key->getId());

        $this->assertEquals($key->getId(), $fromApi->getId());
        $this->assertEquals($key->getName(), $fromApi->getName());
        $this->assertEquals($key->getAlgorithm(), $fromApi->getAlgorithm());
        $this->assertEquals($key->getKey(), $fromApi->getKey());

        // cleanup
        $res = $manager->delete($key);
    }

    /**
     * testing that creating a key with a very weird name works
     *
     * @return void
     */
    public function testCreateWithNonUrlFriendlyName(): void {
        $name = "this/is/not/aa-_412'aur\\asd-url-friendly-" . mt_rand(100, 10000);

        $manager  = $this->powerdns->tsigkeys();
        $resource = new TSIGKeyResource();

        $resource->setName($name);
        $resource->setAlgorithm(TSIGKeyAlgorithms::HMAC_SHA512);

        $key = $manager->create($resource);

        $this->assertNotEquals('', $key->getKey());

        // cleanup
        $manager->delete($key);
    }

    /**
     * testing that the delete function works
     *
     * @return void
     */
    public function testDelete(): void {
        $name = 'tsigkey-' . mt_rand(100, 10000);

        $manager  = $this->powerdns->tsigkeys();
        $resource = new TSIGKeyResource();

        $resource->setName($name);
        $resource->setAlgorithm(TSIGKeyAlgorithms::HMAC_SHA512);

        $key = $manager->create($resource);

        // delete
        $res = $manager->delete($key);

        $this->assertTrue($res);
    }

    /**
     * testing that changing the algorithm works.
     *
     * changing the algo does not regenerate the key.
     *
     * @return void
     */
    public function testChangeAlgorithm(): void {
        $name = 'tsigkey-' . time();

        $manager  = $this->powerdns->tsigkeys();
        $resource = new TSIGKeyResource();

        $resource->setName($name);
        $resource->setAlgorithm(TSIGKeyAlgorithms::HMAC_SHA512);

        $key = $manager->create($resource);

        // update
        $upd = new TSIGKeyResource([
            'id'        => $key->getId(),
            'algorithm' => TSIGKeyAlgorithms::HMAC_SHA256
        ]);

        $updatedKey = $manager->updateAlgorithm($upd);


        $this->assertNotEquals($updatedKey->getAlgorithm(), $key->getAlgorithm());
        $this->assertEquals($updatedKey->getKey(), $key->getKey());

        // // delete
        $res = $manager->delete($key);
    }

    /**
     * testign that changing the name works.
     *
     * As per the note in the powerdns documentation, updating the
     * name does create a new key with the same values as the old key, removing the odl one after copy.
     *
     * @return void
     */
    public function testChangeName(): void {
        $name         = 'tsigkey-' . time();
        $nameToUpdate = 'tsigkey2-' . time();

        $manager  = $this->powerdns->tsigkeys();
        $resource = new TSIGKeyResource();

        $resource->setName($name);
        $resource->setAlgorithm(TSIGKeyAlgorithms::HMAC_SHA512);

        $key = $manager->create($resource);

        // update
        $upd = new TSIGKeyResource([
            'id'   => $key->getId(),
            'name' => $nameToUpdate
        ]);

        $updatedKey = $manager->updateName($upd);


        $this->assertEquals($updatedKey->getAlgorithm(), $key->getAlgorithm());
        // the key does not change when updating the name
        $this->assertEquals($updatedKey->getKey(), $key->getKey());
        $this->assertEquals($updatedKey->getName(), $nameToUpdate);

        // // delete
        $res = $manager->delete($updatedKey);
    }
}
