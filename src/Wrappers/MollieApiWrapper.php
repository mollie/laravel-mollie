<?php
/**
 * Copyright (c) 2016, Mollie B.V.
 * All rights reserved.
 *
 * Redistribution and use in source and binary forms, with or without
 * modification, are permitted provided that the following conditions are met:
 *
 * - Redistributions of source code must retain the above copyright notice,
 *    this list of conditions and the following disclaimer.
 * - Redistributions in binary form must reproduce the above copyright
 *    notice, this list of conditions and the following disclaimer in the
 *    documentation and/or other materials provided with the distribution.
 *
 * THIS SOFTWARE IS PROVIDED BY THE AUTHOR AND CONTRIBUTORS ``AS IS'' AND ANY
 * EXPRESS OR IMPLIED WARRANTIES, INCLUDING, BUT NOT LIMITED TO, THE IMPLIED
 * WARRANTIES OF MERCHANTABILITY AND FITNESS FOR A PARTICULAR PURPOSE ARE
 * DISCLAIMED. IN NO EVENT SHALL THE AUTHOR OR CONTRIBUTORS BE LIABLE FOR ANY
 * DIRECT, INDIRECT, INCIDENTAL, SPECIAL, EXEMPLARY, OR CONSEQUENTIAL DAMAGES
 * (INCLUDING, BUT NOT LIMITED TO, PROCUREMENT OF SUBSTITUTE GOODS OR
 * SERVICES; LOSS OF USE, DATA, OR PROFITS; OR BUSINESS INTERRUPTION) HOWEVER
 * CAUSED AND ON ANY THEORY OF LIABILITY, WHETHER IN CONTRACT, STRICT
 * LIABILITY, OR TORT (INCLUDING NEGLIGENCE OR OTHERWISE) ARISING IN ANY WAY
 * OUT OF THE USE OF THIS SOFTWARE, EVEN IF ADVISED OF THE POSSIBILITY OF SUCH
 * DAMAGE.
 *
 * @license     Berkeley Software Distribution License (BSD-License 2) http://www.opensource.org/licenses/bsd-license.php
 * @author      Mollie B.V. <info@mollie.com>
 * @copyright   Mollie B.V.
 *
 * @link        https://www.mollie.com
 */

namespace Mollie\Laravel\Wrappers;

use Illuminate\Contracts\Config\Repository;
use Mollie\Api\Exceptions\ApiException;
use Mollie\Api\MollieApiClient;

/**
 * Class MollieApiWrapper.
 */
class MollieApiWrapper
{
    /**
     * @var Repository
     */
    protected $config;

    /**
     * @var MollieApiClient
     */
    protected $client;

    /**
     * MollieApiWrapper constructor.
     *
     * @param  Repository  $config
     * @param  MollieApiClient  $client
     * @return void
     *
     * @throws \Mollie\Api\Exceptions\ApiException
     */
    public function __construct(Repository $config, MollieApiClient $client)
    {
        $this->config = $config;
        $this->client = $client;

        $key = $this->config->get('mollie.key');

        if (! empty($key)) {
            $this->setApiKey($key);
        }
    }

    /**
     * @param  string  $url
     */
    public function setApiEndpoint($url)
    {
        $this->client->setApiEndpoint($url);
    }

    /**
     * @return string
     */
    public function getApiEndpoint()
    {
        return $this->client->getApiEndpoint();
    }

    /**
     * @param  string  $api_key The Mollie API key, starting with 'test_' or 'live_'
     *
     * @throws ApiException
     */
    public function setApiKey($api_key)
    {
        $this->client->setApiKey($api_key);
    }

    /**
     * @param  string  $access_token OAuth access token, starting with 'access_'
     *
     * @throws ApiException
     */
    public function setAccessToken($access_token)
    {
        $this->client->setAccessToken($access_token);
    }

    /**
     * @return bool
     */
    public function usesOAuth()
    {
        return $this->client->usesOAuth();
    }

    /**
     * @param $version_string
     * @return \Mollie\Laravel\Wrappers\MollieApiWrapper
     */
    public function addVersionString($version_string)
    {
        $this->client->addVersionString($version_string);

        return $this;
    }

    /**
     * @return \Mollie\Api\Endpoints\PaymentEndpoint
     */
    public function payments()
    {
        return $this->client->payments;
    }

    /**
     * @return \Mollie\Api\Endpoints\PaymentRefundEndpoint
     */
    public function paymentRefunds()
    {
        return $this->client->paymentRefunds;
    }

    /**
     * @return \Mollie\Api\Endpoints\PaymentLinkEndpoint
     */
    public function paymentLinks()
    {
        return $this->client->paymentLinks;
    }

    /**
     * @return \Mollie\Api\Endpoints\MethodEndpoint
     */
    public function methods()
    {
        return $this->client->methods;
    }

    /**
     * @return \Mollie\Api\Endpoints\ProfileMethodEndpoint
     */
    public function profileMethods()
    {
        return $this->client->profileMethods;
    }

    /**
     * @return \Mollie\Api\Endpoints\CustomerEndpoint
     */
    public function customers()
    {
        return $this->client->customers;
    }

    /**
     * @return \Mollie\Api\Endpoints\SettlementsEndpoint
     */
    public function settlements()
    {
        return $this->client->settlements;
    }

    /**
     * @return \Mollie\Api\Endpoints\SubscriptionEndpoint
     */
    public function subscriptions()
    {
        return $this->client->subscriptions;
    }

    /**
     * @return \Mollie\Api\Endpoints\CustomerPaymentsEndpoint
     */
    public function customerPayments()
    {
        return $this->client->customerPayments;
    }

    /**
     * @return \Mollie\Api\Endpoints\MandateEndpoint
     */
    public function mandates()
    {
        return $this->client->mandates;
    }

    /**
     * @return \Mollie\Api\Endpoints\OrganizationEndpoint
     */
    public function organizations()
    {
        return $this->client->organizations;
    }

    /**
     * @return \Mollie\Api\Endpoints\PermissionEndpoint
     */
    public function permissions()
    {
        return $this->client->permissions;
    }

    /**
     * @return \Mollie\Api\Endpoints\InvoiceEndpoint
     */
    public function invoices()
    {
        return $this->client->invoices;
    }

    /**
     * @return \Mollie\Api\Endpoints\ProfileEndpoint
     */
    public function profiles()
    {
        return $this->client->profiles;
    }

    /**
     * @return \Mollie\Api\Endpoints\RefundEndpoint
     */
    public function refunds()
    {
        return $this->client->refunds;
    }

    /**
     * @return \Mollie\Api\Endpoints\ChargebackEndpoint
     */
    public function chargebacks()
    {
        return $this->client->chargebacks;
    }

    /**
     * @return \Mollie\Api\Endpoints\OrderEndpoint
     */
    public function orders()
    {
        return $this->client->orders;
    }

    /**
     * @return \Mollie\Api\Endpoints\OnboardingEndpoint
     */
    public function onboarding()
    {
        return $this->client->onboarding;
    }

    /**
     * @return \Mollie\Api\Endpoints\WalletEndpoint
     */
    public function wallets()
    {
        return $this->client->wallets;
    }

    /**
     * @return \Mollie\Api\Endpoints\ClientEndpoint
     */
    public function clients()
    {
        return $this->client->clients;
    }

    /**
     * @return \Mollie\Api\Endpoints\OrganizationPartnerEndpoint
     */
    public function organizationPartners()
    {
        return $this->client->organizationPartners;
    }

    /**
     * @return void
     *
     * @throws \Mollie\Api\Exceptions\HttpAdapterDoesNotSupportDebuggingException
     */
    public function enableDebugging()
    {
        $this->client->enableDebugging();
    }

    /**
     * @return void
     *
     * @throws \Mollie\Api\Exceptions\HttpAdapterDoesNotSupportDebuggingException
     */
    public function disableDebugging()
    {
        $this->client->disableDebugging();
    }

    /**
     * Handle dynamic property calls.
     *
     * @param  string  $property
     * @return mixed
     */
    public function __get($property)
    {
        if (method_exists($this, $property)) {
            return call_user_func([$this, $property]);
        }

        $message = '%s has no property or method "%s".';

        throw new \Error(
            sprintf($message, static::class, $property)
        );
    }
}
