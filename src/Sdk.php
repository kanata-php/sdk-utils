<?php

namespace Kanata\Sdk;

use Exception;
use Kanata\Sdk\Services\ProxyClient;

abstract class Sdk
{
    protected ProxyClient $client;
    protected string $apiUrl;
    protected string $apiToken;
    protected array $options;

    public function __construct(
        string $token,
        array $options,
        ?ProxyClient $client = null
    ) {
        $this->options = $this->getOptions($options);

        if (!isset($this->options['api-url'])) {
            throw new Exception('The option with the "api-url" is required.');
        }

        $this->apiUrl = $this->options['api-url'];

        $this->apiToken = $token;

        $this->client = $client ?? new ProxyClient($this->options);
    }

    /**
     * Prepare options.
     *
     * @param array $options
     * @return array
     */
    private function getOptions(array $options = []): array
    {
        $defaultOptions = [
            'timeout' => 15.0,
        ];
        return array_merge($defaultOptions, $options);
    }

    protected function request(
        string $method,
        string $url,
        ?string $procedure = null,
        ?array $inputData = null,
        int $expectedStatus = 200,
        string $contentType = 'application/json',
        string $accept = 'application/json',
    ): Response {
        return Request::request(
            client: $this->client,
            apiToken: $this->apiToken,
            method: $method,
            url: $url,
            procedure: $procedure,
            inputData: $inputData,
            expectedStatus: $expectedStatus,
            contentType: $contentType,
            accept: $accept,
        );
    }

    protected function get(
        string $url,
        ?string $procedure = null,
        ?array $inputData = null,
        int $expectedStatus = 200,
        string $contentType = 'application/json',
        string $accept = 'application/json',
    ): Response {
        return $this->request(
            method: 'GET',
            url: $url,
            procedure: $procedure,
            inputData: $inputData,
            expectedStatus: $expectedStatus,
            contentType: $contentType,
            accept: $accept,
        );
    }

    protected function put(
        string $url,
        ?string $procedure = null,
        ?array $inputData = null,
        int $expectedStatus = 200,
        string $contentType = 'application/json',
        string $accept = 'application/json',
    ): Response {
        return $this->request(
            method: 'PUT',
            url: $url,
            procedure: $procedure,
            inputData: $inputData,
            expectedStatus: $expectedStatus,
            contentType: $contentType,
            accept: $accept,
        );
    }

    protected function post(
        string $url,
        ?string $procedure = null,
        ?array $inputData = null,
        int $expectedStatus = 200,
        string $contentType = 'application/json',
        string $accept = 'application/json',
    ): Response {
        return $this->request(
            method: 'POST',
            url: $url,
            procedure: $procedure,
            inputData: $inputData,
            expectedStatus: $expectedStatus,
            contentType: $contentType,
            accept: $accept,
        );
    }

    protected function delete(
        string $url,
        ?string $procedure = null,
        ?array $inputData = null,
        int $expectedStatus = 200,
        string $contentType = 'application/json',
        string $accept = 'application/json',
    ): Response {
        return $this->request(
            method: 'DELETE',
            url: $url,
            procedure: $procedure,
            inputData: $inputData,
            expectedStatus: $expectedStatus,
            contentType: $contentType,
            accept: $accept,
        );
    }
}
