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
use Illuminate\Support\Traits\ForwardsCalls;
use Mollie\Api\MollieApiClient;

/**
 * Class MollieApiWrapper.
 *
 * @property-read \Mollie\Api\Endpoints\BalanceEndpoint $balanceReports
 * @property-read \Mollie\Api\Endpoints\BalanceEndpoint $balances
 * @property-read \Mollie\Api\Endpoints\BalanceTransactionEndpoint $balanceTransactions
 * @property-read \Mollie\Api\Endpoints\ChargebackEndpoint $chargebacks
 * @property-read \Mollie\Api\Endpoints\ClientLinkEndpoint $clientLinks
 * @property-read \Mollie\Api\Endpoints\ClientEndpoint $clients
 * @property-read \Mollie\Api\Endpoints\CustomerPaymentEndpoint $customerPayments
 * @property-read \Mollie\Api\Endpoints\CustomerEndpoint $customers
 * @property-read \Mollie\Api\Endpoints\InvoiceEndpoint $invoices
 * @property-read \Mollie\Api\Endpoints\OnboardingEndpoint $onboarding
 * @property-read \Mollie\Api\Endpoints\OrderLineEndpoint $orderLines
 * @property-read \Mollie\Api\Endpoints\OrderPaymentEndpoint $orderPayments
 * @property-read \Mollie\Api\Endpoints\OrderRefundEndpoint $orderRefunds
 * @property-read \Mollie\Api\Endpoints\OrderEndpoint $orders
 * @property-read \Mollie\Api\Endpoints\OrganizationPartnerEndpoint $organizationPartners
 * @property-read \Mollie\Api\Endpoints\OrganizationEndpoint $organizations
 * @property-read \Mollie\Api\Endpoints\PaymentCaptureEndpoint $paymentCaptures
 * @property-read \Mollie\Api\Endpoints\PaymentChargebackEndpoint $paymentChargebacks
 * @property-read \Mollie\Api\Endpoints\PaymentLinkEndpoint $paymentLinks
 * @property-read \Mollie\Api\Endpoints\PaymentRefundEndpoint $paymentRefunds
 * @property-read \Mollie\Api\Endpoints\PaymentRouteEndpoint $paymentRoutes
 * @property-read \Mollie\Api\Endpoints\PaymentEndpoint $payments
 * @property-read \Mollie\Api\Endpoints\PermissionEndpoint $permissions
 * @property-read \Mollie\Api\Endpoints\ProfileMethodEndpoint $profileMethods
 * @property-read \Mollie\Api\Endpoints\ProfileEndpoint $profiles
 * @property-read \Mollie\Api\Endpoints\MandateEndpoint $mandates
 * @property-read \Mollie\Api\Endpoints\MethodEndpoint $methods
 * @property-read \Mollie\Api\Endpoints\RefundEndpoint $refunds
 * @property-read \Mollie\Api\Endpoints\SettlementCaptureEndpoint $settlementCaptures
 * @property-read \Mollie\Api\Endpoints\SettlementChargebackEndpoint $settlementChargebacks
 * @property-read \Mollie\Api\Endpoints\SettlementPaymentEndpoint $settlementPayments
 * @property-read \Mollie\Api\Endpoints\SettlementRefundEndpoint $settlementRefunds
 * @property-read \Mollie\Api\Endpoints\SettlementEndpoint $settlements
 * @property-read \Mollie\Api\Endpoints\ShipmentEndpoint $shipments
 * @property-read \Mollie\Api\Endpoints\SubscriptionEndpoint $subscriptions
 * @property-read \Mollie\Api\Endpoints\TerminalEndpoint $terminals
 * @property-read \Mollie\Api\Endpoints\WalletEndpoint $wallets
 * @method \Mollie\Api\Endpoints\BalanceEndpoint balanceReports()
 * @method \Mollie\Api\Endpoints\BalanceEndpoint balances()
 * @method \Mollie\Api\Endpoints\BalanceTransactionEndpoint balanceTransactions()
 * @method \Mollie\Api\Endpoints\ChargebackEndpoint chargebacks()
 * @method \Mollie\Api\Endpoints\ClientLinkEndpoint clientLinks()
 * @method \Mollie\Api\Endpoints\ClientEndpoint clients()
 * @method \Mollie\Api\Endpoints\CustomerPaymentEndpoint customerPayments()
 * @method \Mollie\Api\Endpoints\CustomerEndpoint customers()
 * @method \Mollie\Api\Endpoints\InvoiceEndpoint invoices()
 * @method \Mollie\Api\Endpoints\OnboardingEndpoint onboarding()
 * @method \Mollie\Api\Endpoints\OrderLineEndpoint orderLines()
 * @method \Mollie\Api\Endpoints\OrderPaymentEndpoint orderPayments()
 * @method \Mollie\Api\Endpoints\OrderRefundEndpoint orderRefunds()
 * @method \Mollie\Api\Endpoints\OrderEndpoint orders()
 * @method \Mollie\Api\Endpoints\OrganizationPartnerEndpoint organizationPartners()
 * @method \Mollie\Api\Endpoints\OrganizationEndpoint organizations()
 * @method \Mollie\Api\Endpoints\PaymentCaptureEndpoint paymentCaptures()
 * @method \Mollie\Api\Endpoints\PaymentChargebackEndpoint paymentChargebacks()
 * @method \Mollie\Api\Endpoints\PaymentLinkEndpoint paymentLinks()
 * @method \Mollie\Api\Endpoints\PaymentRefundEndpoint paymentRefunds()
 * @method \Mollie\Api\Endpoints\PaymentRouteEndpoint paymentRoutes()
 * @method \Mollie\Api\Endpoints\PaymentEndpoint payments()
 * @method \Mollie\Api\Endpoints\PermissionEndpoint permissions()
 * @method \Mollie\Api\Endpoints\ProfileMethodEndpoint profileMethods()
 * @method \Mollie\Api\Endpoints\ProfileEndpoint profiles()
 * @method \Mollie\Api\Endpoints\MandateEndpoint mandates()
 * @method \Mollie\Api\Endpoints\MethodEndpoint methods()
 * @method \Mollie\Api\Endpoints\RefundEndpoint refunds()
 * @method \Mollie\Api\Endpoints\SettlementCaptureEndpoint settlementCaptures()
 * @method \Mollie\Api\Endpoints\SettlementChargebackEndpoint settlementChargebacks()
 * @method \Mollie\Api\Endpoints\SettlementPaymentEndpoint settlementPayments()
 * @method \Mollie\Api\Endpoints\SettlementRefundEndpoint settlementRefunds()
 * @method \Mollie\Api\Endpoints\SettlementEndpoint settlements()
 * @method \Mollie\Api\Endpoints\ShipmentEndpoint shipments()
 * @method \Mollie\Api\Endpoints\SubscriptionEndpoint subscriptions()
 * @method \Mollie\Api\Endpoints\TerminalEndpoint terminals()
 * @method \Mollie\Api\Endpoints\WalletEndpoint wallets()
 * @method MollieApiWrapper setApiEndpoint(string $url)
 * @method string getApiEndpoint()
 * @method string getVersionStrings()
 * @method MollieApiWrapper setApiKey(string $apiKey)
 * @method MollieApiWrapper setAccessToken(string $accessToken)
 * @method ?bool usesOAuth()
 * @method MollieApiWrapper addVersionString(string $versionString)
 * @method void enableDebugging()
 * @method void disableDebugging()
 * @method MollieApiWrapper setIdempotencyKey(string $key)
 * @method string getIdempotencyKey()
 * @method MollieApiWrapper resetIdempotencyKey()
 * @method MollieApiWrapper setIdempotencyKeyGenerator(\Mollie\Api\Idempotency\IdempotencyKeyGeneratorContract $generator)
 * @method MollieApiWrapper clearIdempotencyKeyGenerator()
 */
class MollieApiWrapper
{
    use ForwardsCalls;

    /**
     * @var Repository
     */
    protected $config;

    /**
     * @var MollieApiClient
     */
    protected $client;

    protected static $supportedClientEndpoints = [
        'balanceReports',
        'balances',
        'balanceTransactions',
        'chargebacks',
        'clientLinks',
        'clients',
        'customerPayments',
        'customers',
        'invoices',
        'onboarding',
        'orderLines',
        'orderPayments',
        'orderRefunds',
        'orders',
        'organizationPartners',
        'organizations',
        'paymentCaptures',
        'paymentChargebacks',
        'paymentLinks',
        'paymentRefunds',
        'paymentRoutes',
        'payments',
        'permissions',
        'profileMethods',
        'profiles',
        'mandates',
        'methods',
        'refunds',
        'settlementCaptures',
        'settlementChargebacks',
        'settlementPayments',
        'settlementRefunds',
        'settlements',
        'shipments',
        'subscriptions',
        'terminals',
        'wallets',
    ];

    /**
     * MollieApiWrapper constructor.
     *
     * @return void
     *
     * @throws \Mollie\Api\Exceptions\ApiException
     */
    public function __construct(Repository $config, MollieApiClient $client)
    {
        $this->config = $config;
        $this->client = $client;

        $key = $this->config->get('mollie.key');

        if (!empty($key)) {
            $this->client->setApiKey($key);
        }
    }

    /**
     * Handle dynamic property calls.
     *
     * @param  string  $property
     * @return mixed
     */
    public function __get($property)
    {
        if ($this->endpointPropertyExists($property)) {
            return $this->client->{$property};
        }

        $message = '%s has no endpoint "%s".';

        throw new \Error(
            sprintf($message, static::class, $property)
        );
    }

    /**
     * Handle dynamic method calls into the Mollie API client.
     *
     * @param  string  $method
     * @param  array  $parameters
     * @return mixed
     *
     * @throws \Mollie\Api\Exceptions\ApiException
     */
    public function __call($method, $parameters)
    {
        if ($this->endpointPropertyExists($method)) {
            return $this->client->{$method};
        }

        return $this->forwardDecoratedCallTo($this->client, $method, $parameters);
    }

    private function endpointPropertyExists(string $property): bool
    {
        return in_array($property, static::$supportedClientEndpoints);
    }
}
