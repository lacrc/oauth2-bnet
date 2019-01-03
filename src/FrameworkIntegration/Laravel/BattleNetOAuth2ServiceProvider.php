<?php

namespace Depotwarehouse\OAuth2\Client\FrameworkIntegration\Laravel;


use Depotwarehouse\OAuth2\Client\Entity\BattleNetUser;
use Depotwarehouse\OAuth2\Client\Provider\SC2Provider;
use Depotwarehouse\OAuth2\Client\Provider\WowProvider;
use Illuminate\Support\ServiceProvider;

class BattleNetOAuth2ServiceProvider extends ServiceProvider
{

    /**
     * Indicates if loading of the provider is deferred.
     *
     * @var bool
     */
    protected $defer = false;

    /**
     * Register the service provider.
     *
     * The Client ID, Secret and Callback may be configured in the .env or in the published config file
     * The .env configuration will take precedence over the config
     */
    public function register() {
        $clientId = env('BNET_CLIENT_ID', config('oauth2-bnet.clientId'));
        $clientSecret = env('BNET_CLIENT_SECRET', config('oauth2-bnet.clientSecret'));
        $redirectUri = env('BNET_CLIENT_REDIRECT_URI', config('oauth2-bnet.redirectUri'));

        $this->app->singleton('Depotwarehouse\OAuth2\Client\Provider\SC2Provider', function()
        use ($clientSecret, $clientId, $redirectUri) {
            return new SC2Provider(compact('clientId', 'clientSecret', 'redirectUri'));
        });

        $this->app->singleton('Depotwarehouse\OAuth2\Client\Provider\WowProvider', function()
        use ($clientSecret, $clientId, $redirectUri) {
            return new WowProvider(compact('clientId', 'clientSecret', 'redirectUri'));
        });
    }

    /**
     * Publishes the configuration file for editing
     */
    public function boot() {
        $this->publishes([
            __DIR__ . '/config/oauth2-bnet.php' => config_path('oauth2-bnet.php')
        ]);

        $this->mergeConfigFrom(__DIR__ . '/config/oauth2-bnet.php', 'oauth2-bnet');
    }
}
