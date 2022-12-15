<?php

namespace Mollie\Laravel;

use Illuminate\Http\Client\Response;
use Illuminate\Support\Facades\Http;
use Mollie\Api\Exceptions\ApiException;
use Mollie\Api\HttpAdapter\MollieHttpAdapterInterface;

class MollieLaravelHttpClientAdapter implements MollieHttpAdapterInterface
{
    private const HTTP_NO_CONTENT = 204;

    public function send($httpMethod, $url, $headers, $httpBody): ?object
    {
        $contentType = $headers['Content-Type'] ?? 'application/json';
        unset($headers['Content-Type']);

        $response = Http::withBody($httpBody, $contentType)
            ->withHeaders($headers)
            ->send($httpMethod, $url);

        return $this->parseResponseBody($response);
    }

    private function parseResponseBody(Response $response): ?object
    {
        $body = $response->body();
        if (empty($body)) {
            if ($response->status() === self::HTTP_NO_CONTENT) {
                return null;
            }

            throw new ApiException('No response body found.');
        }

        $object = @json_decode($body);

        if (json_last_error() !== JSON_ERROR_NONE) {
            throw new ApiException("Unable to decode Mollie response: '{$body}'.");
        }

        if ($response->status() >= 400) {
            throw ApiException::createFromResponse($response->toPsrResponse(), null);
        }

        return $object;
    }

    public function versionString(): string
    {
        return 'Laravel/HttpClient';
    }
}
