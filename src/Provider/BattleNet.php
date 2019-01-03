<?php

namespace Depotwarehouse\OAuth2\Client\Provider;

use Depotwarehouse\OAuth2\Client\Entity\BattleNetUser;
use Guzzle\Http\Exception\BadResponseException;
use League\OAuth2\Client\Exception\IDPException;
use League\OAuth2\Client\Provider\AbstractProvider;
use League\OAuth2\Client\Provider\Exception\IdentityProviderException;
use League\OAuth2\Client\Provider\ResourceOwnerInterface;
use League\OAuth2\Client\Token\AccessToken;
use Psr\Http\Message\ResponseInterface;
use Cache;

/**
 * Class BattleNet
 * League's OAuth2 Implementation for Battle.net API access
 *
 * https://develop.battle.net/documentation/guides
 *
 * @package Depotwarehouse\OAuth2\Client\Provider
 */
abstract class BattleNet extends AbstractProvider {

    /** The game we wish to query. Defaults to SC2. Available options are:
     *  * sc2
     *  * wow
     * @var string
     */
    protected $game;

    /**
     * The Battle.net region we wish to query on. Available options are:
     *  * us
     *  * eu
     *  * kr
     *  * tw
     *  * cn
     *  * sea (sc2-only!)
     *
     * @var string
     */
    protected $region = "us";

    /**
     * BattleNet constructor.
     * @param array $options
     * @param array $collaborators
     */
    public function __construct(array $options = [ ], array $collaborators = [ ]) {
        parent::__construct($options, $collaborators);

        /* We need to validate some data to make sure we haven't constructed in an illegal state. */
        if (!in_array($this->game, [ "sc2", "wow"])) {
            throw new \InvalidArgumentException("Game must be either sc2 or wow , given: {$this->game}");
        }

        /* validate available regions */
        $availableRegions = [ "us", "eu", "kr", "tw", "cn", "sea" ];
        if (!in_array($this->region, $availableRegions)) {
            $regionList = implode(", ", $availableRegions);
            throw new \InvalidArgumentException("Region must be one of: {$regionList}, given: {$this->region}");
        }

        /* sea is only available in sc2 scopes */
        if ($this->region == "sea" && $this->game != "sc2") {
            throw new \InvalidArgumentException("sea region is only available for sc2");
        }
    }

    /**
     * Battle.net API scope separator is a single space. (Last Update: 12/2018)
     * @return string
     */
    protected function getScopeSeparator()
    {
        return " ";
    }

    /**
     * Returns the BaseURL for authenticating with the API by region (Last Update: 12/2018)
     * @return string
     */
    public function getBaseUrl() {
        switch ($this->region) {
            case 'cn':
                return 'https://www.battlenet.com.cn'; /* China specific URL */
            case 'tw':
            case 'kr':
                return "https://apac.battle.net";
            default:
                return "https://{$this->region}.battle.net";
        }
    }

    /**
     * Base Battle.net Authorization URL
     * @return string
     */
    public function getBaseAuthorizationUrl() {
        return $this->getBaseUrl().'/oauth/authorize';
    }

    /**
     * Battle.net URL for getting a Token after authorized (Last Update: 12/2018)
     * @param array $params
     * @return string
     */
    public function getBaseAccessTokenUrl(array $params) {
        return $this->getBaseUrl().'/oauth/token';
    }

    /**
     * Returns the URL to access the Token Owner's info (Battle.net Logged in User)
     * @param AccessToken $token
     * @return string
     */
    public function getResourceOwnerDetailsUrl(AccessToken $token) {
        return $this->getBaseUrl()."/oauth/userinfo?access_token={$token}";
    }

    /**
     * Base endpoint for Game Data APIs
     * @return string
     */
    protected function getGameDataUrl() {
        switch ($this->region) {
            case 'cn':
                return "https://gateway.battlenet.com.cn";
            default:
                return "https://{$this->region}.api.blizzard.com";
        }
    }

    /**
     * Available scopes are wow.profile or sc2.profile
     * @return array
     */
    protected function getDefaultScopes()
    {
        return [
            "{$this->game}.profile"
        ];
    }

    /**
     * Returns the a BattleNetUser of the given $token
     *
     * @param array $response
     * @param AccessToken $token
     * @return BattleNetUser|ResourceOwnerInterface
     */
    protected function createResourceOwner(array $response, AccessToken $token) {
        return new BattleNetUser($response, $this->region);
    }

    /**
     * Returns the authorization headers used by this provider.
     *
     * For authenticated API requests Battle.net uses Bearer {Token} authorization header
     *
     * @param  AccessToken|string|null $token
     * @return array
     */
    protected function getAuthorizationHeaders($token = null) {
        if ($token instanceof AccessToken)
            $token = $token->getToken();

        return [
            "Authorization" => "Bearer $token"
        ];
    }

    /**
     * Checks the API Responses
     *
     * @param ResponseInterface $response
     * @param array|string $data
     * @throws IdentityProviderException
     */
    protected function checkResponse(ResponseInterface $response, $data) {
        if ($response->getStatusCode() != 200) {
            $data = (is_array($data) || empty($data)) ? $data : json_decode($data, true);
            throw new IdentityProviderException($data['error_description'], $response->getStatusCode(), $data);
        }
    }

    /*******************************/
    /*   Client Credentials Flow   *
    /*******************************/

    /**
     * Returns an access token for a Client ID + Client Secret combination
     * https://develop.battle.net/documentation/guides/using-oauth/client-credentials-flow
     *
     * If cache is enabled in options, attempts to check if there's an unexpired key in the cache
     *
     * @param array $options
     *
     * @return \League\OAuth2\Client\Token\AccessTokenInterface
     * @throws \League\OAuth2\Client\Provider\Exception\IdentityProviderException
     */
    public function getClientAccessToken(array $options = []) {
        $cache = config('oauth2-bnet.cache.enabled');
        if ($cache) {
            $drive = config('oauth2-bnet.cache.drive');
            $name = config('oauth2-bnet.cache.name');
        }

        if ($cache && Cache::store($drive)->has($this->game.$name)) {
            $token = Cache::store($drive)->get($this->game.$name);
            if ($token->hasExpired())
                $token = null;
        }

        if (empty($token)) {
            $token = $this->getAccessToken('client_credentials', $options);
        }

        if ($cache && !empty($token)) {
            Cache::store($drive)->put($this->game.$name,  $token, $token->getExpires() - time());
        }

        return $token;
    }
}
