<?php

class BasicAuthConnector extends \Exonet\Powerdns\Connector
{
    private $basicAuthUsername;

    private $basicAuthPassword;

    public function __construct(
        Exonet\Powerdns\Powerdns $client,
        ?GuzzleHttp\HandlerStack $guzzleHandlerStack = null,
        ?string $basicAuthUsername = null,
        ?string $basicAuthPassword = null
    ) {
        parent::__construct($client, $guzzleHandlerStack);

        $this->basicAuthUsername = $basicAuthUsername;
        $this->basicAuthPassword = $basicAuthPassword;
    }

    protected function getDefaultHeaders(): array
    {
        $headers = parent::getDefaultHeaders();

        return [
            ...$headers,
            'Authorization' => 'Basic '.base64_encode($this->basicAuthUsername.':'.$this->basicAuthPassword),
        ];
    }
}
