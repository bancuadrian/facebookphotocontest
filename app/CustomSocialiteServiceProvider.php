<?php
/**
 * Created by PhpStorm.
 * User: bancuadrian
 * Date: 5/18/15
 * Time: 2:44 PM
 */

namespace App;

use Laravel\Socialite\SocialiteServiceProvider;

class CustomSocialiteServiceProvider extends SocialiteServiceProvider {

    /**
     * Indicates if loading of the provider is deferred.
     *
     * @var bool
     */
    protected $defer = true;

    /**
     * Register the service provider.
     *
     * @return void
     */
    public function register()
    {
        $this->app->bindShared('Laravel\Socialite\Contracts\Factory', function ($app) {
            return new CustomSocialiteManager($app);
        });
    }
}