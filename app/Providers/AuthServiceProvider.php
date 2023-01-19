<?php

namespace App\Providers;

use App\Gates\CommentGate;
use App\Gates\PostGate;
use App\Gates\QuestionGate;
use App\Services\Auth\SsoGuard;
use App\Services\Auth\SsoProvider;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;

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
    public function boot() : void
    {
        $this->registerPolicies();

        Auth::provider('sso', function (Application $app) {
            return new SsoProvider($app['hash']);
        });

        Auth::extend('sso', function (Application $app, $name) {
            return new SsoGuard(
                $name,
                $this->app->get(SsoProvider::class),
                $this->app['session.store'],
            );
        });

        Gate::define('show_post', [PostGate::class, 'showPost']);
        Gate::define('like_post', [PostGate::class, 'likePost']);
        Gate::define('store_post', [PostGate::class, 'storePost']);
        Gate::define('update_post', [PostGate::class, 'updatePost']);
        Gate::define('destroy_post', [PostGate::class, 'destroyPost']);

        Gate::define('show_question', [QuestionGate::class, 'showQuestion']);
        Gate::define('like_question', [QuestionGate::class, 'likeQuestion']);
        Gate::define('store_question', [QuestionGate::class, 'storeQuestion']);
        Gate::define('update_question', [QuestionGate::class, 'updateQuestion']);
        Gate::define('destroy_question', [QuestionGate::class, 'destroyQuestion']);

        Gate::define('rate_comment', [CommentGate::class, 'rateComment']);
        Gate::define('store_comment', [CommentGate::class, 'storeComment']);
        Gate::define('update_comment', [CommentGate::class, 'updateComment']);
        Gate::define('destroy_comment', [CommentGate::class, 'destroyComment']);

    }
}
