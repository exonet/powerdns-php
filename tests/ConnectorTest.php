<?php

namespace Exonet\Powerdns\tests;

use Exonet\Powerdns\Connector;
use Exonet\Powerdns\Exceptions\PowerdnsException;
use Exonet\Powerdns\Exceptions\ValidationException;
use Exonet\Powerdns\Powerdns;
use Exonet\Powerdns\Transformers\Transformer;
use GuzzleHttp\Handler\MockHandler;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\Middleware;
use GuzzleHttp\Psr7\Response;
use PHPUnit\Framework\TestCase;
use Psr\Log\NullLogger;

/**
 * @internal
 */
class ConnectorTest extends TestCase
{
    public function testApiCallsAreExecuted()
    {
        $apiCalls = [];
        $mock = new MockHandler(
            [
                // Prepare API responses. One response is returned per API call, in the order they are defined.
                new Response(200, [], json_encode(['server_response' => 'hello world'])),
                new Response(201, [], json_encode(['server_response' => 'hello world'])),
                new Response(204, [], json_encode([])),
                new Response(204, [], json_encode([])),
                new Response(422, [], json_encode(['error' => 'validation error'])),
                new Response(500, [], json_encode(['error' => 'random error'])),
            ]
        );

        $history = Middleware::history($apiCalls);
        $handler = HandlerStack::create($mock);
        $handler->push($history);

        $powerDnsClient = \Mockery::mock(Powerdns::class);
        $powerDnsClient->shouldReceive('log')->andReturn(new NullLogger());
        $powerDnsClient->shouldReceive('getConfig')->withNoArgs()->andReturn(
            ['host' => 'http://test', 'port' => 1234, 'server' => 'localhost', 'apiKey' => 'very_secret_key']
        );

        $transformer = \Mockery::mock(Transformer::class);
        $transformer->shouldReceive('transform')->andReturn(['transformed' => 'data']);

        $connectorClass = new Connector($powerDnsClient, $handler);

        // Perform a GET/POST/PATCH/DELETE. Normal responses (can be empty) are expected.
        $this->assertSame(['server_response' => 'hello world'], $connectorClass->get('test'));
        $this->assertSame(['server_response' => 'hello world'], $connectorClass->post('test', $transformer));
        $this->assertSame([], $connectorClass->patch('test', $transformer));
        $this->assertSame([], $connectorClass->delete('test'));

        /*
         * The following two calls will throw exceptions. '$this->expectException' can not be used, as the execution of
         * the test stops after the first exception. By using this workaround for catching exceptions, the whole test
         * method is executed.
         */
        $validationExceptionIsThrown = false;
        $powerDnsExceptionIsThrown = false;

        try {
            $connectorClass->get('test');
        } catch (ValidationException $exception) {
            $this->assertSame('validation error', $exception->getMessage());
            $validationExceptionIsThrown = true;
        }

        try {
            $connectorClass->get('test');
        } catch (PowerdnsException $exception) {
            $this->assertSame('random error', $exception->getMessage());
            $powerDnsExceptionIsThrown = true;
        }

        // Assert the aftermath of the different API calls.
        $this->assertTrue($validationExceptionIsThrown);
        $this->assertTrue($powerDnsExceptionIsThrown);
        $this->assertCount(6, $apiCalls);

        foreach ($apiCalls as $apiCall) {
            /** @var \GuzzleHttp\Psr7\Request $request */
            $request = $apiCall['request'];
            $this->assertSame('/api/v1/servers/localhost/test', $request->getUri()->getPath());
            $this->assertSame('very_secret_key', $request->getHeader('X-API-Key')[0]);
            $this->assertSame('application/json', $request->getHeader('Accept')[0]);
            $this->assertSame('exonet-powerdns-php/'.Powerdns::CLIENT_VERSION, $request->getHeader('User-Agent')[0]);
        }
    }
}
