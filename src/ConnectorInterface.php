<?php

declare(strict_types=1);

namespace Exonet\Powerdns;

use Exonet\Powerdns\Transformers\Transformer;

interface ConnectorInterface
{
    /**
     * Perform a GET request and return the parsed body as response.
     *
     * @param string $urlPath The URL path.
     *
     * @return mixed[] The response body.
     */
    public function get(string $urlPath): array;

    /**
     * Perform a POST request and return the parsed body as response.
     *
     * @param string      $urlPath The URL path.
     * @param Transformer $payload The payload to post.
     *
     * @return mixed[] The response body.
     */
    public function post(string $urlPath, Transformer $payload): array;

    /**
     * Perform a PATCH request and return the parsed body as response.
     *
     * @param string      $urlPath The URL path.
     * @param Transformer $payload The payload to patch.
     *
     * @return mixed[] The response body.
     */
    public function patch(string $urlPath, Transformer $payload): array;

    /**
     * Perform a PUT request and return the parsed body as response.
     *
     * @param string           $urlPath The URL path.
     * @param Transformer|null $payload The payload to put.
     *
     * @return mixed[] The response body.
     */
    public function put(string $urlPath, Transformer $payload = null): array;

    /**
     * Perform a DELETE request and return the parsed body as response.
     *
     * @param string $urlPath The URL path.
     *
     * @return mixed[] The response body.
     */
    public function delete(string $urlPath): array;
}
