<?php

declare(strict_types=1);

namespace Exonet\Powerdns;

use Exonet\Powerdns\Exceptions\PowerdnsException;
use Exonet\Powerdns\Exceptions\ValidationException;
use Exonet\Powerdns\Transformers\Transformer;
use GuzzleHttp\Client as GuzzleClient;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\Psr7\Request;
use GuzzleHttp\Psr7\Response as PsrResponse;
use GuzzleHttp\Psr7\Utils;

class Connector implements ConnectorInterface
{
    /**
     * @var GuzzleClient The Guzzle client.
     */
    private $httpClient;

    /**
     * @var Powerdns The API client.
     */
    private $powerdns;

    /**
     * Connector constructor.
     *
     * @param Powerdns|null     $client             The client instance.
     * @param HandlerStack|null $guzzleHandlerStack Optional Guzzle handlers.
     */
    public function __construct(Powerdns $client, ?HandlerStack $guzzleHandlerStack = null)
    {
        $this->powerdns = $client;

        // Don't let Guzzle throw exceptions, as it is handled by this class.
        $this->httpClient = new GuzzleClient(['exceptions' => false, 'handler' => $guzzleHandlerStack]);
    }

    /**
     * Perform a GET request and return the parsed body as response.
     *
     * @param string $urlPath The URL path.
     *
     * @return mixed[] The response body.
     */
    public function get(string $urlPath): array
    {
        return $this->makeCall('GET', $urlPath);
    }

    /**
     * Perform a POST request and return the parsed body as response.
     *
     * @param string      $urlPath The URL path.
     * @param Transformer $payload The payload to post.
     *
     * @return mixed[] The response body.
     */
    public function post(string $urlPath, Transformer $payload): array
    {
        return $this->makeCall('POST', $urlPath, json_encode($payload->transform()));
    }

    /**
     * Perform a PATCH request and return the parsed body as response.
     *
     * @param string      $urlPath The URL path.
     * @param Transformer $payload The payload to patch.
     *
     * @return mixed[] The response body.
     */
    public function patch(string $urlPath, Transformer $payload): array
    {
        return $this->makeCall('PATCH', $urlPath, json_encode($payload->transform()));
    }

    /**
     * Perform a PUT request and return the parsed body as response.
     *
     * @param string           $urlPath The URL path.
     * @param Transformer|null $payload The payload to put.
     *
     * @return mixed[] The response body.
     */
    public function put(string $urlPath, ?Transformer $payload = null): array
    {
        return $this->makeCall('PUT', $urlPath, $payload !== null ? json_encode($payload->transform()) : null);
    }

    /**
     * Perform a DELETE request and return the parsed body as response.
     *
     * @param string $urlPath The URL path.
     *
     * @return mixed[] The response body.
     */
    public function delete(string $urlPath): array
    {
        return $this->makeCall('DELETE', $urlPath);
    }

    /**
     * Make the call to the Powerdns API.
     *
     * @param string      $method  The method to use for the call.
     * @param string      $urlPath The URL path.
     * @param string|null $payload (Optional) The payload to include.
     *
     * @throws PowerdnsException   When an unknown response is returned.
     * @throws ValidationException When a validation error is returned.
     *
     * @return mixed[] The decoded JSON response.
     */
    protected function makeCall(string $method, string $urlPath, ?string $payload = null): array
    {
        $url = $this->buildUrl($urlPath);
        $headers = $this->getDefaultHeaders();

        $this->powerdns->log()->debug('Sending ['.$method.'] request', compact('url', 'headers', 'payload'));

        $stream = $payload !== null ? Utils::streamFor($payload) : null;
        $request = new Request($method, $url, $headers, $stream);

        $response = $this->httpClient->send($request, ['http_errors' => false]);

        return $this->parseResponse($response);
    }

    /**
     * Parse the call response.
     *
     * @param PsrResponse $response The call response.
     *
     * @throws ValidationException If there was a validation error returned.
     * @throws PowerdnsException   If there was a problem with the request.
     *
     * @return mixed[] The decoded JSON response.
     */
    protected function parseResponse(PsrResponse $response): array
    {
        $this->powerdns->log()->debug('Request completed', ['statusCode' => $response->getStatusCode()]);
        $contents = json_decode($response->getBody()->getContents(), true);

        switch ($response->getStatusCode()) {
            case 200:
            case 201:
                return $contents ?? [];

                break;

            case 204:
                return [];

                break;

            case 422:
                throw new ValidationException($contents['error']);

                break;
        }

        $this->powerdns->log()->debug('Request failed.', ['result_body' => $contents]);
        $error = $contents['error'] ?? 'Unknown PowerDNS exception.';

        throw new PowerdnsException($error);
    }

    /**
     * Get the complete URL for making API requests.
     *
     * @param string $path The path to append to the "base" URL.
     *
     * @return string The complete URL.
     */
    protected function buildUrl(string $path): string
    {
        $config = $this->powerdns->getConfig();

        return rtrim(
            sprintf(
                '%s:%d/api/v1/servers/%s/%s',
                $config['host'],
                $config['port'],
                $config['server'],
                $path
            ),
            '/'
        );
    }

    /**
     * Get the headers that are default for each request.
     *
     * @return string[] The headers.
     */
    protected function getDefaultHeaders(): array
    {
        return [
            'X-API-Key' => $this->powerdns->getConfig()['apiKey'],
            'Accept' => 'application/json',
            'Content-Type' => 'application/json',
            'User-Agent' => 'exonet-powerdns-php/'.Powerdns::CLIENT_VERSION,
        ];
    }
}
