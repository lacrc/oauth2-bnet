<?php

namespace Depotwarehouse\OAuth2\Client\Provider;

use League\OAuth2\Client\Token\AccessToken;

/**
 * Class WowProvider
 *
 * WoW specific Endpoints available in the Documentation
 *
 * World of Warcraft player profiles
 * https://develop.battle.net/documentation/api-reference/world-of-warcraft-community-api
 *
 * World of Warcraft Mythics, realms, and other game information
 * https://develop.battle.net/documentation/api-reference/world-of-warcraft-game-data-api
 *
 * World of Warcraft character profiles, including Mythic Keystone profiles and seasonal profiles
 * https://develop.battle.net/documentation/api-reference/world-of-warcraft-profile-api
 *
 * @package Depotwarehouse\OAuth2\Client\Provider
 */
class WowProvider extends BattleNet {

    /**
     * @var string
     */
    protected $game = "wow";

    /*************************/
    /*   Authorization Flow
    /*************************/

    /**
     * Returns an Array of Wow characters for the Battle.net user owner of $token
     * @param AccessToken $token
     * @return mixed
     * @throws \League\OAuth2\Client\Provider\Exception\IdentityProviderException
     */
    public function getCharactersArray(AccessToken $token) {
        $url = $this->getGameDataUrl()."/wow/user/characters";

        $request = $this->getAuthenticatedRequest(self::METHOD_GET, $url, $token);

        return $this->getParsedResponse($request);
    }
}
