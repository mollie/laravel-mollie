<?php

declare(strict_types=1);

namespace Mollie\Laravel;

use Illuminate\Http\Client\ConnectionException;
use Illuminate\Http\Client\RequestException;
use Illuminate\Support\Facades\Http;
use Mollie\Api\Contracts\HasPayload;
use Mollie\Api\Contracts\HttpAdapterContract;
use Mollie\Api\Exceptions\ApiException;
use Mollie\Api\Exceptions\RetryableNetworkRequestException;
use Mollie\Api\Http\PendingRequest;
use Mollie\Api\Http\Response;
use Mollie\Api\Traits\HasDefaultFactories;

class MollieLaravelHttpClientAdapter implements HttpAdapterContract
{
    use HasDefaultFactories;

    /**
     * Get the version string for this HTTP adapter.
     */
    public function version(): ?string
    {
        return 'Laravel/HttpClient';
    }

    /**
     * Send a request to the specified Mollie API URL.
     *
     * @throws ApiException
     */
    public function sendRequest(PendingRequest $pendingRequest): Response
    {
        $psrRequest = $pendingRequest->createPsrRequest();

        try {
            $client = Http::withHeaders($pendingRequest->headers()->all());

            if ($pendingRequest->query()->isNotEmpty()) {
                $client = $client->withUrlParameters($pendingRequest->query()->all());
            }

            if ($pendingRequest->getRequest() instanceof HasPayload) {
                $client = $client->withBody($psrRequest->getBody());
            }

            $psrResponse = $client
                ->send($pendingRequest->method(), $pendingRequest->url())
                ->toPsrResponse();

            return new Response($psrResponse, $psrRequest, $pendingRequest);
        } catch (ConnectionException $e) {
            throw new RetryableNetworkRequestException($pendingRequest, $e->getMessage(), $e);
        } catch (RequestException $e) {
            // RequestExceptions without response are handled by the retryable network request exception
            return new Response($e->response->toPsrResponse(), $psrRequest, $pendingRequest, $e);
        }
    }
}
