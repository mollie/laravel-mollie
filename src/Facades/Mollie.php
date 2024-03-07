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

namespace Mollie\Laravel\Facades;

use Illuminate\Support\Facades\Facade;
use Mollie\Api\MollieApiClient;

/**
 * (Facade) Class Mollie.
 *
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
 * @method MollieApiClient setApiEndpoint(string $url)
 * @method string getApiEndpoint()
 * @method string getVersionStrings()
 * @method MollieApiClient setApiKey(string $apiKey)
 * @method MollieApiClient setAccessToken(string $accessToken)
 * @method ?bool usesOAuth()
 * @method MollieApiClient addVersionString(string $versionString)
 * @method void enableDebugging()
 * @method void disableDebugging()
 * @method MollieApiClient setIdempotencyKey(string $key)
 * @method string getIdempotencyKey()
 * @method MollieApiClient resetIdempotencyKey()
 * @method MollieApiClient setIdempotencyKeyGenerator(\Mollie\Api\Idempotency\IdempotencyKeyGeneratorContract $generator)
 * @method MollieApiClient clearIdempotencyKeyGenerator()
 */
class Mollie extends Facade
{
    /**
     * Get the registered name of the component.
     *
     * @return string
     */
    protected static function getFacadeAccessor()
    {
        return MollieApiClient::class;
    }
}
