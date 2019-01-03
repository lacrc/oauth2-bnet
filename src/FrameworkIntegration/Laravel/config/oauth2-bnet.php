<?php

return [
    /**
     * clientId and clientSecret can be found in:
     * https://develop.battle.net/access/clients
     */
    'clientId' => env('BNET_CLIENT_ID'),
    'clientSecret' => env('BNET_CLIENT_SECRET'),

    /**
     * redirectUri is the URI that users will be redirected after entering
     * valid credentials in the Battle.Net login page (aka. Callback URL)
     */
    'redirectUri' => env('BNET_CLIENT_REDIRECT_URI'),

    /**
     * Cache Configuration (Client Credentials flow ONLY)
     *
     * Caches the token in the defined cache
     * Attempts to recover a token from cache to reduce API requests
     */
    'cache' => [
        'enabled' => true,
        'drive' => 'file',
        /* cache name format will be {game}name, game is defined in the providers */
        'name' => '_battlenet_client_token',
    ],
];
