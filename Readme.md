
# Sdk Utilities

This is a helper package to provide tools for SDKs.

## Usage

```php
use Kanata\Sdk\Sdk;
use Kanata\Sdk\Request;
use Kanata\Sdk\Response;

class ExampleSdk extends Sdk
{
    public function getResource(): Response
    {
        return $this->get(
            url: $this->options['api-url'] . '/api/resource',
        );
    }
}

$token = 'some-token-here';
$options = [
    'api-url' => 'https://some-url',
];
$exampleSdk = new ExampleSdk($token, $options);
$response = $exampleSdk->getResource();
```

> **Info:** this SDK at this moment expects the API to expect the `Token` at the header `Authorization: Bearer $token` model.

## Installation

```shell
composer require kanata-php/sdk-util
```

## Motivation

The routine of building clients for APIs always goes through the same steps. Some of those are contained in this package.
