<?php

namespace Mollie\Laravel;

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
     * Send a request to the specified Mollie API URL.
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
        $headers = $pendingRequest->headers();
        
        // Execute request handlers from middleware
        $pendingRequest->executeRequestHandlers();
        
        // Create PSR-7 request using factories
        $psrRequest = $this->factories()
            ->requestFactory
            ->createRequest($method, $url);

        // Convert headers from ArrayStore to plain array
        $headersArray = [];
        foreach ($headers->all() as $key => $value) {
            $headersArray[$key] = $value;
        }

        // Configure Laravel HTTP client with headers
        $httpClient = Http::withHeaders($headersArray);
            
        // Send request using Laravel HTTP client
        try {
            $laravelResponse = $httpClient->send($method, $url);
            
            // Convert Laravel response to PSR-7 response
            $psrResponse = $laravelResponse->toPsrResponse();
            
            // Create and return Mollie Response
            return new Response($psrResponse, $psrRequest, $pendingRequest);
        } catch (\Exception $e) {
            // Create a generic error response
            $factory = $this->factories()->responseFactory;
            $psrErrorResponse = $factory->createResponse(500);
            
            // Create a Mollie Response with the error
            $errorResponse = new Response($psrErrorResponse, $psrRequest, $pendingRequest);
            
            throw new ApiException($errorResponse, $e->getMessage(), $e->getCode(), $e);
        }
    }
}
