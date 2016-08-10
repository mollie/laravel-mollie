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
 * @link        https://www.mollie.com
 */
namespace Mollie\Laravel\Wrappers;

use Illuminate\Contracts\Config\Repository;
use Mollie_API_Client;

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
     * @var Mollie_API_Client
     */
    protected $client;

    /**
     * MollieApiWrapper constructor.
     *
     * @param Repository $config
     * @param Mollie_API_Client $client
     *
     * @return void
     */
    public function __construct(Repository $config, Mollie_API_Client $client)
    {
        $this->config = $config;

        $this->client = $client;

        // Use only the 'live_' API key when 'test_mode' is DISABLED.
        if (! $this->config->get('mollie.test_mode')) {
            if ($this->config->has('mollie.keys.live')) {
                $this->setApiKey($this->config->get('mollie.keys.live'));
            }
        } else {
            if ($this->config->has('mollie.keys.test')) {
                $this->setApiKey($this->config->get('mollie.keys.test'));
            }
        }
    }

    /**
     * @param $url
     *
     * @return void
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
     * @param $api_key
     *
     * @return void
     *
     * @throws \Mollie_API_Exception
     */
    public function setApiKey($api_key)
    {
        $this->client->setApiKey($api_key);
    }

    /**
     * @param $access_token
     *
     * @return void
     *
     * @throws \Mollie_API_Exception
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
     * @return \Mollie_API_Resource_Payments
     */
    public function payments()
    {
        return $this->client->payments;
    }

    /**
     * @return \Mollie_API_Resource_Payments_Refunds
     */
    public function paymentsRefunds()
    {
        return $this->client->payments_refunds;
    }

    /**
     * @return \Mollie_API_Resource_Issuers
     */
    public function issuers()
    {
        return $this->client->issuers;
    }

    /**
     * @return \Mollie_API_Resource_Methods
     */
    public function methods()
    {
        return $this->client->methods;
    }

    /**
     * @return \Mollie_API_Resource_Customers
     */
    public function customers()
    {
        return $this->client->customers;
    }

    /**
     * @return \Mollie_API_Resource_Customers_Payments
     */
    public function customersPayments()
    {
        return $this->client->customers_payments;
    }

    /**
     * @return \Mollie_API_Resource_Customers_Mandates
     */
    public function customersMandates()
    {
        return $this->client->customers_mandates;
    }

    /**
     * @return \Mollie_API_Resource_Customers_Subscriptions
     */
    public function customersSubscriptions()
    {
        return $this->client->customers_subscriptions;
    }

    /**
     * @return \Mollie_API_Resource_Permissions
     */
    public function permissions()
    {
        return $this->client->permissions;
    }

    /**
     * @return \Mollie_API_Resource_Organizations
     */
    public function organizations()
    {
        return $this->client->organizations;
    }

    /**
     * @return \Mollie_API_Resource_Profiles
     */
    public function profiles()
    {
        return $this->client->profiles;
    }

    /**
     * @return \Mollie_API_Resource_Refunds
     */
    public function refunds()
    {
        return $this->client->refunds;
    }

    /**
     * @return \Mollie_API_Resource_Settlements
     */
    public function settlements()
    {
        return $this->client->settlements;
    }
}
