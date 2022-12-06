<?php

namespace KanataSdk;

use Exception;
use GuzzleHttp\Exception\ClientException;
use GuzzleHttp\Exception\ConnectException;
use KanataSdk\Services\ProxyClient;

class Request
{
    /**
     * @param ProxyClient $client
     * @param string $apiToken
     * @param string $method
     * @param string $url
     * @param string $procedure
     * @param array|null $inputData
     * @param int $expectedStatus
     * @return Response
     * @throws Exception
     */
    public static function request(
        ProxyClient $client,
        string $apiToken,
        string $method,
        string $url,
        ?string $procedure = null,
        ?array $inputData = null,
        int $expectedStatus = 200,
        string $contentType = 'application/json',
        string $accept = 'application/json',
        ?string $inputDataWrapper = 'data',
    ): Response {
        if (null === $procedure) {
            $procedure = $method;
        }

        switch ($method) {

            case 'POST':
                return self::post(
                    client: $client,
                    apiToken: $apiToken,
                    url: $url,
                    procedure: $procedure,
                    inputData: $inputData,
                    expectedStatus: $expectedStatus,
                    contentType: $contentType,
                    accept: $accept,
                    inputDataWrapper: $inputDataWrapper,
                );

            case 'PUT':
                return self::put(
                    client: $client,
                    apiToken: $apiToken,
                    url: $url,
                    procedure: $procedure,
                    inputData: $inputData,
                    expectedStatus: $expectedStatus,
                    contentType: $contentType,
                    accept: $accept,
                    inputDataWrapper: $inputDataWrapper,
                );

            case 'GET':
                return self::get(
                    client: $client,
                    apiToken: $apiToken,
                    url: $url,
                    procedure: $procedure,
                    expectedStatus: $expectedStatus,
                    contentType: $contentType,
                    accept: $accept,
                );

            case 'DELETE':
                return self::delete(
                    client: $client,
                    apiToken: $apiToken,
                    url: $url,
                    procedure: $procedure,
                    expectedStatus: $expectedStatus,
                    contentType: $contentType,
                    accept: $accept,
                    inputDataWrapper: $inputDataWrapper,
                );

            default:
                throw new Exception('Not implemented method: ' . $method);

        }
    }

    public static function post(
        ProxyClient $client,
        string $apiToken,
        string $url,
        string $procedure,
        ?array $inputData = null,
        int $expectedStatus = 200,
        string $contentType = 'application/json',
        string $accept = 'application/json',
        ?string $inputDataWrapper = 'data',
    ): Response {
        $payload = $inputData;
        if (null !== $inputDataWrapper) {
            $payload = [$inputDataWrapper => $inputData];
        }

        $result = new Response;

        try {
            $response = $client->request('POST', $url, [
                'headers' => [
                    'Authorization' => 'Bearer ' . $apiToken,
                    'Content-Type' => $contentType,
                    'Accept' => $accept,
                ],
                'body' => json_encode($payload),
            ]);
        } catch (ConnectException $e) {
            $result->setFormattedResponse(
                status: 408,
                success: false,
                message: 'Request timeout!',
                error: $e->getMessage(),
                debug: [
                    'request-info' => [
                        'request-url' => $url,
                        'request-body' => $payload,
                    ],
                ]
            );
            return $result;
        } catch (ClientException $e) {
            $response = $e->getResponse();
            $data = json_decode($response->getBody()->getContents(), true);
            $result->setFormattedResponse(
                $response->getStatusCode(),
                success:false,
                message: $data['message'] ?? 'There was an error with procedure ' . $procedure . '!',
                debug: [
                    'request-info' => [
                        'request-url' => $url,
                        'request-body' => $payload,
                    ],
                    'response-body' => $data,
                ]
            );
            return $result;
        } catch (Exception $e) {
            $result->setFormattedResponse(
                status: 500,
                success: false,
                message: 'There was an error with procedure ' . $procedure . '!',
                error: $e->getMessage(),
                debug: [
                    'request-info' => [
                        'request-url' => $url,
                        'request-body' => $payload,
                    ],
                ]
            );
            return $result;
        }

        $result->status = $response->getStatusCode();

        if ($expectedStatus === $response->getStatusCode()) {
            $data = json_decode($response->getBody()->getContents(), true);
            $result->setFormattedResponse(
                status: $expectedStatus,
                success: true,
                data: $data['data'] ?? $data
            );
            return $result;
        }

        if (403 === $response->getStatusCode()) {
            $result->setFormattedResponse(
                403,
                success:false,
                message: 'Forbidden!',
                debug: [
                    'request-info' => [
                        'request-url' => $url,
                        'request-body' => $payload,
                    ],
                    'response-body' => json_decode($response->getBody()->getContents(), true),
                ]
            );
            return $result;
        }

        $result->setFormattedResponse(
            status: 500,
            success: false,
            message: 'Unknown Error!',
            debug: [
                'request-info' => [
                    'request-url' => $url,
                    'request-body' => $payload,
                ],
                'response-status' => $response->getStatusCode(),
                'response-body' => json_decode($response->getBody()->getContents(), true),
            ]
        );

        return $result;
    }

    public static function put(
        ProxyClient $client,
        string $apiToken,
        string $url,
        string $procedure,
        ?array $inputData = null,
        int $expectedStatus = 200,
        string $contentType = 'application/json',
        string $accept = 'application/json',
    ): Response {
        $payload = ['data' => $inputData];

        $result = new Response;

        try {
            $response = $client->request('PUT', $url, [
                'headers' => [
                    'Authorization' => 'Bearer ' . $apiToken,
                    'Content-Type' => $contentType,
                    'Accept' => $accept,
                ],
                'body' => json_encode($payload),
            ]);
        } catch (ConnectException $e) {
            $result->setFormattedResponse(
                status: 408,
                success: false,
                message: 'Request timeout!',
                error: $e->getMessage(),
                debug: [
                    'request-info' => [
                        'request-url' => $url,
                        'request-body' => $payload,
                    ],
                ]
            );
            return $result;
        } catch (ClientException $e) {
            $response = $e->getResponse();
            $data = json_decode($response->getBody()->getContents(), true);
            $result->setFormattedResponse(
                $response->getStatusCode(),
                success:false,
                message: $data['message'] ?? 'There was an error with procedure ' . $procedure . '!',
                debug: [
                    'request-info' => [
                        'request-url' => $url,
                        'request-body' => $payload,
                    ],
                    'response-body' => $data,
                ]
            );
            return $result;
        } catch (Exception $e) {
            $result->setFormattedResponse(
                status: 500,
                success: false,
                message: 'There was an error with procedure ' . $procedure . '!',
                error: $e->getMessage(),
                debug: [
                    'request-info' => [
                        'request-url' => $url,
                        'request-body' => $payload,
                    ],
                ]
            );
            return $result;
        }

        $result->status = $response->getStatusCode();

        if ($expectedStatus === $response->getStatusCode()) {
            $data = json_decode($response->getBody()->getContents(), true);
            $result->setFormattedResponse(
                status: $expectedStatus,
                success: true,
                data: $data['data'] ?? $data
            );
            return $result;
        }

        if (403 === $response->getStatusCode()) {
            $result->setFormattedResponse(
                403,
                success:false,
                message: 'Forbidden!',
                debug: [
                    'request-info' => [
                        'request-url' => $url,
                        'request-body' => $payload,
                    ],
                    'response-body' => json_decode($response->getBody()->getContents(), true),
                ]
            );
            return $result;
        }

        $result->setFormattedResponse(
            status: 500,
            success: false,
            message: 'Unknown Error!',
            debug: [
                'request-info' => [
                    'request-url' => $url,
                    'request-body' => $payload,
                ],
                'response-status' => $response->getStatusCode(),
                'response-body' => json_decode($response->getBody()->getContents(), true),
            ]
        );

        return $result;
    }

    public static function get(
        ProxyClient $client,
        string $apiToken,
        string $url,
        string $procedure,
        int $expectedStatus = 200,
        string $contentType = 'application/json',
        string $accept = 'application/json',
    ): Response {
        $result = new Response;

        try {
            $response = $client->request('GET', $url, [
                'headers' => [
                    'Authorization' => 'Bearer ' . $apiToken,
                    'Content-Type' => $contentType,
                    'Accept' => $accept,
                ],
            ]);
        } catch (ConnectException $e) {
            $result->setFormattedResponse(
                status: 408,
                success: false,
                message: 'Request timeout!',
                error: $e->getMessage(),
                debug: [
                    'request-info' => [
                        'request-url' => $url,
                    ],
                ]
            );
            return $result;
        } catch (ClientException $e) {
            $response = $e->getResponse();
            $data = json_decode($response->getBody()->getContents(), true);
            $result->setFormattedResponse(
                $response->getStatusCode(),
                success:false,
                message: $data['message'] ?? 'There was an error with procedure ' . $procedure . '!',
                debug: [
                    'request-info' => [
                        'request-url' => $url,
                    ],
                    'response-body' => $data,
                ]
            );
            return $result;
        } catch (Exception $e) {
            $result->setFormattedResponse(
                status: 500,
                success: false,
                message: 'There was an error with procedure ' . $procedure . '!',
                error: $e->getMessage(),
                debug: [
                    'request-info' => [
                        'request-url' => $url,
                    ],
                ]
            );
            return $result;
        }

        $result->status = $response->getStatusCode();

        if ($expectedStatus === $response->getStatusCode()) {
            if ('application/json' === $accept) {
                $data = json_decode($response->getBody()->getContents(), true);
                $data = $data['data'] ?? $data;
            } else {
                $data = '';
                $body = $response->getBody();
                while (!$body->eof()) {
                    $data .= $body->read(1024);
                }
            }
            $result->setFormattedResponse(
                status: $expectedStatus,
                success: true,
                data: $data,
            );
            return $result;
        }

        if (404 === $response->getStatusCode()) {
            $result->setFormattedResponse(
                status: 404,
                success: true,
            );
            return $result;
        }

        if (403 === $response->getStatusCode()) {
            $result->setFormattedResponse(
                403,
                success:false,
                message: 'Forbidden!',
                debug: [
                    'request-info' => [
                        'request-url' => $url,
                    ],
                    'response-body' => json_decode($response->getBody()->getContents(), true),
                ]
            );
            return $result;
        }

        $result->setFormattedResponse(
            status: 500,
            success: false,
            message: 'Unknown Error!',
            debug: [
                'request-info' => [
                    'request-url' => $url,
                ],
                'response-status' => $response->getStatusCode(),
                'response-body' => json_decode($response->getBody()->getContents(), true),
            ]
        );

        return $result;
    }

    public static function delete(
        ProxyClient $client,
        string $apiToken,
        string $url,
        string $procedure,
        int $expectedStatus = 200,
        string $contentType = 'application/json',
        string $accept = 'application/json',
    ): Response {
        $result = new Response;

        try {
            $response = $client->request('DELETE', $url, [
                'headers' => [
                    'Authorization' => 'Bearer ' . $apiToken,
                    'Content-Type' => $contentType,
                    'Accept' => $accept,
                ],
            ]);
        } catch (ConnectException $e) {
            $result->setFormattedResponse(
                status: 408,
                success: false,
                message: 'Request timeout!',
                error: $e->getMessage(),
                debug: [
                    'request-info' => [
                        'request-url' => $url,
                    ],
                ]
            );
            return $result;
        } catch (ClientException $e) {
            $response = $e->getResponse();
            $data = json_decode($response->getBody()->getContents(), true);
            $result->setFormattedResponse(
                $response->getStatusCode(),
                success:false,
                message: $data['message'] ?? 'There was an error with procedure ' . $procedure . '!',
                debug: [
                    'request-info' => [
                        'request-url' => $url,
                    ],
                    'response-body' => $data,
                ]
            );
            return $result;
        } catch (Exception $e) {
            $result->setFormattedResponse(
                status: 500,
                success: false,
                message: 'There was an error with procedure ' . $procedure . '!',
                error: $e->getMessage(),
                debug: [
                    'request-info' => [
                        'request-url' => $url,
                    ],
                ]
            );
            return $result;
        }

        $result->status = $response->getStatusCode();

        if ($expectedStatus === $response->getStatusCode()) {
            if ('application/json' === $accept) {
                $data = json_decode($response->getBody()->getContents(), true);
                $data = $data['data'] ?? null;
            } else {
                $data = '';
                $body = $response->getBody();
                while (!$body->eof()) {
                    $data .= $body->read(1024);
                }
            }
            $result->setFormattedResponse(
                status: $expectedStatus,
                success: true,
                data: $data,
            );
            return $result;
        }

        if (404 === $response->getStatusCode()) {
            $result->setFormattedResponse(
                status: 404,
                success: true,
            );
            return $result;
        }

        if (403 === $response->getStatusCode()) {
            $result->setFormattedResponse(
                403,
                success:false,
                message: 'Forbidden!',
                debug: [
                    'request-info' => [
                        'request-url' => $url,
                    ],
                    'response-body' => json_decode($response->getBody()->getContents(), true),
                ]
            );
            return $result;
        }

        $result->setFormattedResponse(
            status: 500,
            success: false,
            message: 'Unknown Error!',
            debug: [
                'request-info' => [
                    'request-url' => $url,
                ],
                'response-status' => $response->getStatusCode(),
                'response-body' => json_decode($response->getBody()->getContents(), true),
            ]
        );

        return $result;
    }
}
