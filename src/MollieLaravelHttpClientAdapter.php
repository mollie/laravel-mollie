<?php

namespace Mollie\Laravel;

use Illuminate\Http\Client\Response as LaravelResponse;
use Illuminate\Support\Facades\Http;
use Mollie\Api\Exceptions\ApiException;
use Mollie\Api\Contracts\HttpAdapterContract;
use Mollie\Api\Http\PendingRequest;
use Mollie\Api\Http\Response;
use Mollie\Api\Utils\Factories;
use Nyholm\Psr7\Factory\Psr17Factory;

class MollieLaravelHttpClientAdapter implements HttpAdapterContract
{
    /**
     * Get the HTTP factories used by this adapter.
     *
     * @return Factories
     */
    public function factories(): Factories
    {
        $psr17Factory = new Psr17Factory();
        
        return new Factories(
            $psr17Factory, // RequestFactoryInterface
            $psr17Factory, // ResponseFactoryInterface
            $psr17Factory, // StreamFactoryInterface
            $psr17Factory  // UriFactoryInterface
        );
    }

    /**
     * Get the version string for this HTTP adapter.
     *
     * @return string|null
     */
    public function version(): ?string
    {
        return 'Laravel/HttpClient';
    }

    /**
     * Send a request to the specified Mollie api url.
     *
     * @param PendingRequest $pendingRequest
     * @return Response
     * @throws ApiException
     */
    public function sendRequest(PendingRequest $pendingRequest): Response
    {
        // Get request details from PendingRequest
        $method = $pendingRequest->method();
        $url = $pendingRequest->url();
        
        // Prepare headers for Laravel HTTP client
        $headers = [];
        
        // Execute request handlers from middleware
        $pendingRequest->executeRequestHandlers();
        
        // Create PSR-7 request using factories
        $psrRequest = $this->factories()
            ->requestFactory
            ->createRequest($method, $url);

        $httpClient = Http::withHeaders($headers);
            
        // Send request using Laravel HTTP client
        try {
            $laravelResponse = $httpClient->send($method, $url);
            
            // Convert Laravel response to PSR-7 response
            $psrResponse = $laravelResponse->toPsrResponse();
            
            // Create and return Mollie Response
            return new Response($psrResponse, $psrRequest, $pendingRequest);
        } catch (\Exception $e) {
            throw new ApiException($psrResponse, $e->getMessage(), $e->getCode(), $e);
        }
    }
    
    /**
     * @deprecated Use sendRequest() instead.
     * @param string $httpMethod
     * @param string $url
     * @param array $headers
     * @param string $httpBody
     * @return LaravelResponse
     * @throws ApiException
     */
    public function send(string $httpMethod, string $url, array $headers, string $httpBody): LaravelResponse
    {
        $contentType = $headers['Content-Type'] ?? 'application/json';
        unset($headers['Content-Type']);

        try {
            $response = Http::withBody($httpBody, $contentType)
                ->withHeaders($headers)
                ->send($httpMethod, $url);
                
            return $response;
        } catch (\Exception $e) {
            throw new ApiException($e->getMessage(), $e->getCode(), $e);
        }
    }
}
