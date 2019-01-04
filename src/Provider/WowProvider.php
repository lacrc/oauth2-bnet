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

    /**
     * Returns a Character Profile information in array format.
     *
     * For more info about available $fields check Character Profile API in:
     * https://develop.battle.net/documentation/api-reference/world-of-warcraft-community-api
     *
     * @param AccessToken $token
     * @param $realm
     * @param $name
     * @param null $fields
     * @return mixed
     * @throws \League\OAuth2\Client\Provider\Exception\IdentityProviderException
     */
    public function getCharacterProfile(AccessToken $token, $realm, $name, $fields = null) {
        $realm = strtolower($realm);
        $url = $this->getGameDataUrl()."/wow/character/$realm/$name";

        $valid_fields = [
            'achievements', 'appearance', 'feed', 'guild', 'hunterPets', 'itens', 'mounts', 'pets',
            'petSlots', 'professions', 'progression', 'pvp', 'quests', 'reputation', 'statistics',
            'stats', 'talents', 'titles', 'audit',
        ];

        if (!empty($fields) && is_array($fields) && !empty(array_intersect($valid_fields, $fields))) {
            $url .= '?fields='.implode(",", $fields);
        }

        $request = $this->getAuthenticatedRequest(self::METHOD_GET, $url, $token);
        return $this->getParsedResponse($request);
    }

    /**
     * Returns a Guild Profile in array format.
     *
     * For more info about available $fields check Guild Profile API in:
     * https://develop.battle.net/documentation/api-reference/world-of-warcraft-community-api
     *
     * @param AccessToken $token
     * @param $realm
     * @param $guild_name
     * @param null $fields
     * @return mixed
     * @throws \League\OAuth2\Client\Provider\Exception\IdentityProviderException
     */
    public function getGuildProfile(AccessToken $token, $realm, $guild_name, $fields = null) {
        $realm = strtolower($realm);
        $url = $this->getGameDataUrl()."/wow/character/$realm/$guild_name";

        $valid_fields = [
            'achievements', 'members', 'news', 'challenge',
        ];

        if (!empty($fields) && is_array($fields) && !empty(array_intersect($valid_fields, $fields))) {
            $url .= '?fields='.implode(",", $fields);
        }

        $request = $this->getAuthenticatedRequest(self::METHOD_GET, $url, $token);
        return $this->getParsedResponse($request);
    }

}
