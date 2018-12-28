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

abstract class BattleNet extends AbstractProvider {

    /**
     * Battle.net OAuth2 Documentation:
     * https://develop.battle.net/documentation/api-reference/oauth-api
     *
     */

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

    public function __construct(array $options = [ ], array $collaborators = [ ])
    {
        parent::__construct($options, $collaborators);

        // We need to validate some data to make sure we haven't constructed in an illegal state.
        if (!in_array($this->game, [ "sc2", "wow"])) {
            throw new \InvalidArgumentException("Game must be either sc2 or wow, given: {$this->game}");
        }

        $availableRegions = [ "us", "eu", "kr", "tw", "cn", "sea" ];
        if (!in_array($this->region, $availableRegions)) {
            $regionList = implode(", ", $availableRegions);
            throw new \InvalidArgumentException("Region must be one of: {$regionList}, given: {$this->region}");
        }

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
}
