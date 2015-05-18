<?php
/**
 * Created by PhpStorm.
 * User: bancuadrian
 * Date: 5/18/15
 * Time: 2:46 PM
 */

namespace App;


use Laravel\Socialite\SocialiteManager;

class CustomSocialiteManager extends SocialiteManager{
    /**
     * Create an instance of the specified driver.
     *
     * @return \Laravel\Socialite\Two\AbstractProvider
     */
    protected function createFacebookDriver()
    {
        $config = $this->app['config']['services.facebook'];

        return $this->buildProvider(
            'App\CustomFacebookProvider', $config
        );
    }
}