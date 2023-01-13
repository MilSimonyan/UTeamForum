<?php

namespace App\Services\Auth;

use App\Entities\User;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;
use Illuminate\Auth\EloquentUserProvider;
use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Contracts\Hashing\Hasher as HasherContract;
use Illuminate\Support\Facades\Cookie;

class AuthProvider extends EloquentUserProvider
{
    public function __construct(HasherContract $hasher, $model = null)
    {
        parent::__construct($hasher, $model);
    }

    /**
     * Retrieve a user by their unique identifier.
     * Method is not relevant for our authentication mechanism.
     *
     * @param mixed $identifier
     *
     * @return \Illuminate\Contracts\Auth\Authenticatable|null
     */
    public function retrieveById($identifier) : ?Authenticatable
    {
        return null;
    }

    /**
     * Retrieve a user by their unique identifier and "remember me" token.
     * Method is not relevant for our authentication mechanism.
     *
     * @param mixed  $identifier
     * @param string $token
     *
     * @return \Illuminate\Contracts\Auth\Authenticatable|null
     */
    public function retrieveByToken($identifier, $token) : ?Authenticatable
    {
        return null;
    }

    /**
     * Update the "remember me" token for the given user in storage.
     * Method is not relevant for our authentication mechanism.
     *
     * @param \Illuminate\Contracts\Auth\Authenticatable $user
     * @param string                                     $token
     *
     * @return void
     */
    public function updateRememberToken(Authenticatable $user, $token) : void
    {
    }

    /**
     * Retrieve a user by the given credentials.
     * Method is not relevant for our authentication mechanism.
     *
     * @param array $credentials
     *
     * @return \Illuminate\Contracts\Auth\Authenticatable|null
     */
    public function retrieveByCredentials(array $credentials) : ?Authenticatable
    {
        return null;
    }

    /**
     * Validate a user against the given credentials.
     * Method is not relevant for our authentication mechanism.
     *
     * @param \Illuminate\Contracts\Auth\Authenticatable $user
     * @param array                                      $credentials
     *
     * @return bool
     */
    public function validateCredentials(Authenticatable $user, array $credentials) : bool
    {
        return true;
    }

    /**
     * Retrieve a user by sso gateway.
     *
     * @return \Illuminate\Contracts\Auth\Authenticatable|null
     */
    public function retrieveByAuthGateway() : null|string
    {
        try {
            $client = new Client();
            $session = Cookie::get('laravel_session');

            $user_response = $client->get("http://account.u-team.com/user", [
                'headers' => [
                    'Cookie' => "laravel_session={$session}"
                ]
            ]);
dd($user_response->getBody()->getContents());
            $user = User::fromJson(json_decode($user_response->getBody())->data);//TODO will be a change remove data

            return $user;
        } catch (GuzzleException $ex) {
            return null;
        }
    }
}
