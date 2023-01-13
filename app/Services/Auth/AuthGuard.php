<?php

namespace App\Services\Auth;

use App\Entities\User;
use Illuminate\Auth\SessionGuard;
use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Contracts\Session\Session;
use Illuminate\Support\Timebox;
use Symfony\Component\HttpFoundation\Request;

class AuthGuard extends SessionGuard
{
    /**
     * The user provider implementation.
     *
     * @var \App\Services\Auth\AuthProvider $provider
     */
    protected $provider;

    public function __construct($name,
                                AuthProvider $provider,
                                Session $session,
                                Request $request = null,
                                Timebox $timebox = null)
    {
        parent::__construct($name, $provider, $session, $request, $timebox);

        $this->setCookieJar(app('cookie'));
    }

    /**
     * Get the currently authenticated user.
     *
     * @return \App\Entities\User|\Illuminate\Contracts\Auth\Authenticatable|null
     */
    public function user() : null|User|Authenticatable
    {
        if ($this->user !== null) {
            return $this->user;
        }

        if (!($this->user = $this->provider->retrieveByAuthGateway())) {
            return null;
        }

        return $this->user;
    }
}
