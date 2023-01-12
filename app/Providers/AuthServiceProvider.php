<?php

namespace App\Providers;

// use Illuminate\Support\Facades\Gate;
use App\Services\Auth\AuthGuard;
use App\Services\Auth\AuthProvider;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Auth;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * The model to policy mappings for the application.
     *
     * @var array<class-string, class-string>
     */
    protected $policies = [
        // 'App\Models\Model' => 'App\Policies\ModelPolicy',
    ];

    /**
     * Register any authentication / authorization services.
     *
     * @return void
     */
    public function boot()
    {
        $this->registerPolicies();

        Auth::provider('auth', function (Application $app, array $config) {
            return new AuthProvider($app['hash']);
        });

        Auth::extend('auth', function (Application $app, $name, array $config) {
            return new AuthGuard(
                $name,
                $this->app->get(AuthProvider::class),
                $this->app['session.store'],
            );
        });


        //
    }
}
