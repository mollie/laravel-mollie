<?php

namespace Mollie\Laravel;

use Laravel\Socialite\Two\AbstractProvider;
use Laravel\Socialite\Two\ProviderInterface;
use Laravel\Socialite\Two\User;

/**
 * Class MollieConnectProvider.
 */
class MollieConnectProvider extends AbstractProvider implements ProviderInterface
{
    /**
     * The base url to the Mollie API.
     *
     * @const string
     */
    const MOLLIE_API_URL = 'https://api.mollie.com';

    /**
     * The base url to the Mollie web application.
     *
     * @const string
     */
    const MOLLIE_WEB_URL = 'https://www.mollie.com';

    /**
     * The scopes being requested.
     *
     * @var array
     */
    protected $scopes = ['organizations.read'];

    /**
     * The separating character for the requested scopes.
     *
     * @var string
     */
    protected $scopeSeparator = ' ';

    /**
     * Get the authentication URL for the provider.
     *
     * @param  string  $state
     * @return string
     */
    protected function getAuthUrl($state)
    {
        return $this->buildAuthUrlFromBase(static::MOLLIE_WEB_URL.'/oauth2/authorize', $state);
    }

    /**
     * Get the token URL for the provider.
     *
     * @return string
     */
    protected function getTokenUrl()
    {
        return static::MOLLIE_API_URL.'/oauth2/tokens';
    }

    /**
     * Get the raw user for the given access token.
     *
     * @param  string  $token
     * @return array
     */
    protected function getUserByToken($token)
    {
        $response = $this->getHttpClient()->get(static::MOLLIE_API_URL.'/v2/organizations/me', [
            'headers' => ['Authorization' => 'Bearer '.$token],
        ]);

        return json_decode($response->getBody(), true);
    }

    /**
     * Map the raw user array to a Socialite User instance.
     *
     * @return \Laravel\Socialite\AbstractUser
     */
    protected function mapUserToObject(array $user)
    {
        return (new User())->setRaw($user)->map([
            'id' => $user['id'],
            'nickname' => $user['name'],
            'name' => $user['name'],
            'email' => $user['email'],
            'avatar' => null,
        ]);
    }
}
