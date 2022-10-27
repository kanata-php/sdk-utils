<?php

namespace Kanata\Sdk\Services;

use GuzzleHttp\Client;

class ProxyClient
{
    protected Client $client;
    protected array $options;
    protected $response;

    public function __construct(array $options)
    {
        $this->options = $options;
        $this->client = new Client([
            'timeout' => $this->options['timeout'],
        ]);
    }

    public function post(string $url, array $params)
    {
        $this->response = $this->client->post($url, $params);
        return $this;
    }

    public function getStatusCode(): ?int
    {
        return $this->response->getStatusCode();
    }

    public function getBody()
    {
        return $this;
    }

    public function getContents(): string
    {
        return $this->response->getBody()->getContents();
    }

    public function __call(string $name, array $arguments)
    {
        return call_user_func_array([$this->client, $name], $arguments);
    }
}
