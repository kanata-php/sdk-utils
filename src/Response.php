<?php

namespace KanataSdk;

use ArrayAccess;
use JsonSerializable;

class Response implements JsonSerializable, ArrayAccess
{
    protected array $data;

    public function __get(string $name): mixed
    {
        return isset($this->data[$name]) ? $this->data[$name] : null;
    }

    public function __set(string $name, $value): void
    {
        $this->data[$name] = $value;
    }

    public function jsonSerialize()
    {
        return $this->data;
    }

    public function offsetExists(mixed $offset): bool
    {
        return isset($this->data[$offset]);
    }

    public function offsetGet(mixed $offset): mixed
    {
        return $this->data[$offset];
    }

    public function offsetSet(mixed $offset, mixed $value): void
    {
        $this->data[$offset] = $value;
    }

    public function offsetUnset(mixed $offset): void
    {
        unset($this->data[$offset]);
    }

    /**
     * Set an opinionated response format.
     *
     * @param int $status
     * @param bool $success
     * @param mixed $data
     * @param string|null $message
     * @param string|null $error
     * @param mixed $debug
     * @return void
     */
    public function setFormattedResponse(
        int $status,
        bool $success,
        mixed $data = null,
        ?string $message = null,
        ?string $error = null,
        mixed $debug = null
    ): void {
        $this->data['status'] = $status;
        $this->data['success'] = $success;

        if (null !== $data) {
            $this->data['data'] = $data;
        }

        if (null !== $message) {
            $this->data['message'] = $message;
        }

        if (null !== $error) {
            $this->data['error'] = $error;
        }

        if (null !== $debug) {
            $this->data['debug'] = $debug;
        }
    }

    public function setErrorResponse(\GuzzleHttp\Psr7\Response $response, string $requestInfo)
    {
        $this->setFormattedResponse(
            status: 500,
            success: false,
            message: 'Unknown Error!',
            debug: [
                'request-info' => $requestInfo,
                'response-status' => $response->getStatusCode(),
                'response-body' => $response->getBody()->getContents(),
            ]
        );

        return $this->data;
    }

    /**
     * Get an opinionated response format.
     *
     * @return array
     */
    public function getFormattedResponse(): array
    {
        $success = $this->success ?? false;
        $data = ['success' => $success];

        if ($success) {
            $data['data'] = $this->data['data'];
        } else {
            $data['message'] = $this->message;
        }

        if ($this->error) {
            $data['error'] = $this->error;
        }

        return $data;
    }
}
