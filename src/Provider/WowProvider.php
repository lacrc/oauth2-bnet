<?php

namespace Depotwarehouse\OAuth2\Client\Provider;

use League\OAuth2\Client\Token\AccessTokenInterface as AccessToken;
use League\OAuth2\Client\Token\AccessTokenInterface;

/**
 * Class WowProvider
 *
 * WoW specific Resources available in the Documentation organized by API according to the Battle.net Docs
 *
 * Community API - World of Warcraft player profiles
 * https://develop.battle.net/documentation/api-reference/world-of-warcraft-community-api
 *
 * Game Data API - World of Warcraft Mythics, realms, and other game information
 * https://develop.battle.net/documentation/api-reference/world-of-warcraft-game-data-api
 *
 * Profile API - World of Warcraft character profiles, including Mythic Keystone profiles and seasonal profiles
 * https://develop.battle.net/documentation/api-reference/world-of-warcraft-profile-api
 *
 * @package Depotwarehouse\OAuth2\Client\Provider
 */
class WowProvider extends BattleNet {

    /**
     * @var string
     */
    protected $game = "wow";

    /**
     * Returns the region-specific namespace for Game Data Resources
     *
     * Made this simple function so it would be easier to change namespaces
     * throughout the game data resources in case of a Blizzard update
     *
     * @return string
     */
    private function getNamespace($namespace) {
        return $namespace.'-'.$this->getBattleNetRegion();
    }

    /*********************/
    /*   Community APIs
    /*********************/

    /**
     * World of Warcraft Profile API
     * Returns an Array of WoW characters for the Battle.net user owner of $token (authorization_flow)
     * @param AccessToken $token
     * @return array
     * @throws \League\OAuth2\Client\Provider\Exception\IdentityProviderException
     */
    public function getCharacters(AccessToken $token) {
        $url = $this->getGameDataUrl()."/wow/user/characters";

        $request = $this->getAuthenticatedRequest(self::METHOD_GET, $url, $token);

        return $this->getParsedResponse($request);
    }

    /**
     * Achievement API - Returns information about a single Achievement
     * @param AccessToken $token
     * @param integer $id
     * @return array
     * @throws \League\OAuth2\Client\Provider\Exception\IdentityProviderException
     */
    public function getAchievement(AccessToken $token, $id) {
        $url = $this->getGameDataUrl()."/wow/achievement/{$id}";

        $request = $this->getAuthenticatedRequest(self::METHOD_GET, $url, $token);

        return $this->getParsedResponse($request);
    }

    /**
     * Auction API - Returns a per-realm list of recently generated auction house data dumps
     * @param AccessToken $token
     * @param string $realm
     * @return array
     * @throws \League\OAuth2\Client\Provider\Exception\IdentityProviderException
     */
    public function getAuctionDumpList(AccessToken $token, $realm) {
        $url = $this->getGameDataUrl()."/wow/auction/data/{$realm}";

        $request = $this->getAuthenticatedRequest(self::METHOD_GET, $url, $token);

        return $this->getParsedResponse($request);
    }

    /**
     * Boss API - Returns a list of boss encounters
     * (may contain more than one NPC depending on encounter)
     * @param AccessToken $token
     * @return array
     * @throws \League\OAuth2\Client\Provider\Exception\IdentityProviderException
     */
    public function getBossNPCList(AccessToken $token) {
        $url = $this->getGameDataUrl()."/wow/boss/";

        $request = $this->getAuthenticatedRequest(self::METHOD_GET, $url, $token);

        return $this->getParsedResponse($request);
    }

    /**
     * Boss API - Returns information about a specific Boss encounter
     * (may contain more than one NPC depending on encounter)
     * @param AccessToken $token
     * @param integer $id
     * @return array
     * @throws \League\OAuth2\Client\Provider\Exception\IdentityProviderException
     */
    public function getBoss(AccessToken $token, $id) {
        $url = $this->getGameDataUrl()."/wow/boss/{$id}";

        $request = $this->getAuthenticatedRequest(self::METHOD_GET, $url, $token);

        return $this->getParsedResponse($request);
    }

    /**
     * Challenge Mode API - Returns information about the current CM leaderboards
     * @param AccessToken $token
     * @param string $realm
     * @return array
     * @throws \League\OAuth2\Client\Provider\Exception\IdentityProviderException
     */
    public function getCMRealmLeaderboard(AccessToken $token, $realm) {
        $url = $this->getGameDataUrl()."/wow/challenge/{$realm}";

        $request = $this->getAuthenticatedRequest(self::METHOD_GET, $url, $token);

        return $this->getParsedResponse($request);
    }

    /**
     * Challenge Mode API - Returns information about the top 100 runs for each map for the region
     * @param AccessToken $token
     * @return array
     * @throws \League\OAuth2\Client\Provider\Exception\IdentityProviderException
     */
    public function getCMRegionLeaderboard(AccessToken $token) {
        $url = $this->getGameDataUrl()."/wow/challenge/region";

        $request = $this->getAuthenticatedRequest(self::METHOD_GET, $url, $token);

        return $this->getParsedResponse($request);
    }

    /**
     * Character Profile API - Returns information about a single character.
     *
     * For more info about available $fields check Character Profile API in:
     * https://develop.battle.net/documentation/api-reference/world-of-warcraft-community-api
     *
     * @param AccessToken $token
     * @param string $realm
     * @param string $name
     * @param null $fields
     * @return array
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
     * Guild Profile API - Returns a information about a single guild.
     *
     * @param AccessToken $token
     * @param string $realm
     * @param string $guild_name
     * @param null $fields
     * @return array
     * @throws \League\OAuth2\Client\Provider\Exception\IdentityProviderException
     */
    public function getGuildProfile(AccessToken $token, $realm, $guild_name, $fields = null) {
        $realm = strtolower($realm);
        $url = $this->getGameDataUrl()."/wow/guild/$realm/$guild_name";

        $valid_fields = [
            'achievements', 'members', 'news', 'challenge',
        ];

        if (!empty($fields) && is_array($fields) && !empty(array_intersect($valid_fields, $fields))) {
            $url .= '?fields='.implode(",", $fields);
        }

        $request = $this->getAuthenticatedRequest(self::METHOD_GET, $url, $token);
        return $this->getParsedResponse($request);
    }

    /**
     * Item API - Returns information about an Item given the $id
     * @param AccessToken $token
     * @param integer $id
     * @return array
     * @throws \League\OAuth2\Client\Provider\Exception\IdentityProviderException
     */
    public function getItem(AccessToken $token, $id) {
        $url = $this->getGameDataUrl()."/wow/item/{$id}";

        $request = $this->getAuthenticatedRequest(self::METHOD_GET, $url, $token);
        return $this->getParsedResponse($request);
    }

    /**
     * Item API - Returns information about an Item Set given the $set_id
     * @param AccessToken $token
     * @param integer $set_id
     * @return array
     * @throws \League\OAuth2\Client\Provider\Exception\IdentityProviderException
     */
    public function getItemSet(AccessToken $token, $set_id) {
        $url = $this->getGameDataUrl()."/wow/set/{$set_id}";

        $request = $this->getAuthenticatedRequest(self::METHOD_GET, $url, $token);
        return $this->getParsedResponse($request);
    }

    /**
     * Mount API - Returns a List of all supported mounts
     * @param AccessToken $token
     * @return array
     * @throws \League\OAuth2\Client\Provider\Exception\IdentityProviderException
     */
    public function getMount(AccessToken $token) {
        $url = $this->getGameDataUrl()."/wow/mount";

        $request = $this->getAuthenticatedRequest(self::METHOD_GET, $url, $token);
        return $this->getParsedResponse($request);
    }

    /**
     * Pet API - Returns a master list of all supported battle pets and vanity pets
     * @param AccessToken $token
     * @return array
     * @throws \League\OAuth2\Client\Provider\Exception\IdentityProviderException
     */
    public function getPetMasterList(AccessToken $token) {
        $url = $this->getGameDataUrl()."/wow/pet";

        $request = $this->getAuthenticatedRequest(self::METHOD_GET, $url, $token);
        return $this->getParsedResponse($request);
    }

    /**
     * Pet API - Returns data about a individual battle pet ability ID.
     * (This resource does not provide ability tooltips)
     * @param AccessToken $token
     * @param integer $ability_id
     * @return array
     * @throws \League\OAuth2\Client\Provider\Exception\IdentityProviderException
     */
    public function getPetAbilities(AccessToken $token, $ability_id) {
        $url = $this->getGameDataUrl()."/wow/pet/ability/{$ability_id}";

        $request = $this->getAuthenticatedRequest(self::METHOD_GET, $url, $token);
        return $this->getParsedResponse($request);
    }

    /**
     * Pet API - Returns data about an individual pet species.
     * (Use pets as the field value in a character profile request to get species IDs)
     * @param AccessToken $token
     * @param integer $species_id
     * @return array
     * @throws \League\OAuth2\Client\Provider\Exception\IdentityProviderException
     */
    public function getPetSpecies(AccessToken $token, $species_id) {
        $url = $this->getGameDataUrl()."/wow/pet/species/{$species_id}";

        $request = $this->getAuthenticatedRequest(self::METHOD_GET, $url, $token);
        return $this->getParsedResponse($request);
    }

    /**
     * Pet API - Returns detailed information about a given species of pet.
     * (Use pets as the field value in a character profile request to get species IDs)
     * @param AccessToken $token
     * @param integer $species_id
     * @param int $level = 1
     * @param int $breed = 3
     * @param int $quality = 1
     * @return array
     * @throws \League\OAuth2\Client\Provider\Exception\IdentityProviderException
     */
    public function getPetStats(AccessToken $token, $species_id, $level = 1, $breed = 3, $quality = 1) {
        $url = $this->getGameDataUrl()."/wow/pet/stats/{$species_id}";

        $extra_fields = [
            'level' => $level,
            'breedId' => $breed,
            'qualityId' => $quality,
        ];

        $request = $this->getAuthenticatedRequest(self::METHOD_GET, $url, $token, ['header' => $extra_fields]);
        return $this->getParsedResponse($request);
    }

    /**
     * PVP API - Returns leaderboard information for the 2v2, 3v3, 5v5, and Rated Battleground leaderboards.
     * @param AccessToken $token
     * @param string $bracket
     * @return array
     * @throws \League\OAuth2\Client\Provider\Exception\IdentityProviderException
     */
    public function getPVPLeaderboard(AccessToken $token, $bracket) {
        $valid_brackets = ['2v2', '3v3', '5v5', 'rgb'];

        if (!in_array($bracket, $valid_brackets))
            return [
                'message' => 'Invalid bracket informed. Valid entries are 2v2, 3v3, 5v5, and rbg.'
            ];

        $url = $this->getGameDataUrl()."/wow/leaderboard/{$bracket}";

        $request = $this->getAuthenticatedRequest(self::METHOD_GET, $url, $token);
        return $this->getParsedResponse($request);
    }

    /**
     * Quest API - Returns metadata for a specified quest.
     * @param AccessToken $token
     * @param integer $quest_id
     * @return array
     * @throws \League\OAuth2\Client\Provider\Exception\IdentityProviderException
     */
    public function getQuest(AccessToken $token, $quest_id) {
        $url = $this->getGameDataUrl()."/wow/quest/{$quest_id}";

        $request = $this->getAuthenticatedRequest(self::METHOD_GET, $url, $token);
        return $this->getParsedResponse($request);
    }

    /**
     * Realm Status API - Returns realm status information.
     * This information is limited to whether or not the realm is up,
     * the type and state of the realm, and the current population.
     * @param AccessToken $token
     * @param integer $realms (optional) limit of realms to retrieve
     * @return array
     * @throws \League\OAuth2\Client\Provider\Exception\IdentityProviderException
     */
    public function getRealms(AccessToken $token, $realms = null) {
        $url = $this->getGameDataUrl()."/wow/realm/status";

        $request = $this->getAuthenticatedRequest(self::METHOD_GET, $url, $token,
            (!empty($realms) ? ['realms' => $realms] : []));
        return $this->getParsedResponse($request);
    }

    /**
     * Recipe API - Returns basic recipe information.
     * @param AccessToken $token
     * @param integer $recipe_id
     * @return array
     * @throws \League\OAuth2\Client\Provider\Exception\IdentityProviderException
     */
    public function getRecipe(AccessToken $token, $recipe_id) {
        $url = $this->getGameDataUrl()."/wow/recipe/{$recipe_id}";

        $request = $this->getAuthenticatedRequest(self::METHOD_GET, $url, $token);
        return $this->getParsedResponse($request);
    }


    /**
     * Recipe API - Returns information about a spell.
     * @param AccessToken $token
     * @param integer $spell_id
     * @return array
     * @throws \League\OAuth2\Client\Provider\Exception\IdentityProviderException
     */
    public function getSpell(AccessToken $token, $spell_id) {
        $url = $this->getGameDataUrl()."/wow/spell/{$spell_id}";

        $request = $this->getAuthenticatedRequest(self::METHOD_GET, $url, $token);
        return $this->getParsedResponse($request);
    }

    /**
     * User API - Characters is the same as the already declared getCharacters(...) function.
     * If Blizzard review this resource in the future it will be updated.
     */

    /**
     * Zone API - Returns a list of all supported zones and their bosses.
     * (A "zone" in this context should be considered a dungeon or a raid, not a world zone)
     * @param AccessToken $token
     * @return array
     * @throws \League\OAuth2\Client\Provider\Exception\IdentityProviderException
     */
    public function getZoneList(AccessToken $token) {
        $url = $this->getGameDataUrl()."/wow/zone/";

        $request = $this->getAuthenticatedRequest(self::METHOD_GET, $url, $token);
        return $this->getParsedResponse($request);
    }

    /**
     * Zone API - Returns information about zones.
     * @param AccessToken $token
     * @param integer $zone_id
     * @return array
     * @throws \League\OAuth2\Client\Provider\Exception\IdentityProviderException
     */
    public function getZone(AccessToken $token, $zone_id) {
        $url = $this->getGameDataUrl()."/wow/zone/{$zone_id}";

        $request = $this->getAuthenticatedRequest(self::METHOD_GET, $url, $token);
        return $this->getParsedResponse($request);
    }

    /**
     * Data Resources - Returns wow data information
     *
     * @param AccessToken $token
     * @param string $field
     * @return array
     * @throws \League\OAuth2\Client\Provider\Exception\IdentityProviderException
     */
    public function getWowData(AccessToken $token, $field) {
        switch ($field) {
            case 'battlegroups':
                $url = '/wow/data/battlegroups/';
                break;
            case 'character_races':
                $url = '/wow/data/character/races';
                break;
            case 'character_classes':
                $url = '/wow/data/character/classes';
                break;
            case 'character_achievements':
                $url = '/wow/data/character/achievements';
                break;
            case 'guild_rewards':
                $url = '/wow/data/guild/rewards';
                break;
            case 'guild_perks':
                $url = '/wow/data/guild/perks';
                break;
            case 'guild_achievements':
                $url = '/wow/data/guild/achievements';
                break;
            case 'item_classes':
                $url = '/wow/data/item/classes';
                break;
            case 'talents':
                $url = '/wow/data/talents';
                break;
            case 'pet_types':
                $url = '/wow/data/pet/types';
                break;
            default:
                return [
                    'message' => 'Invalid field. Valid fields are: '.
                        'battlegroups, character_races, character_classes, character_achievements, '.
                        'guild_rewards, guild_perks, guild_achievements, item_classes, talents, pet_types'
                ];
        }

        $url = $this->getGameDataUrl() . $url;
        $request = $this->getAuthenticatedRequest(self::METHOD_GET, $url, $token);
        return $this->getParsedResponse($request);
    }

    /**********************/
    /*   Game Data APIs   *
    /**********************/

    /**
     * Connected Realm API - Returns an index of connected realms.
     * @param AccessToken $token
     * @return array
     * @throws \League\OAuth2\Client\Provider\Exception\IdentityProviderException
     */
    public function getConnectedRealmIndex(AccessToken $token) {
        $url = $this->getGameDataUrl() . '/data/wow/connected-realm/index'."?namespace={$this->getNamespace('dynamic')}";

        $request = $this->getAuthenticatedRequest(self::METHOD_GET, $url, $token);
        return $this->getParsedResponse($request);
    }

    /**
     * Connected Realm API - Returns a single connected realm by ID.
     * @param AccessToken $token
     * @param integer $realm_id
     * @return array
     * @throws \League\OAuth2\Client\Provider\Exception\IdentityProviderException
     */
    public function getConnectedRealm(AccessToken $token, $realm_id) {
        $url = $this->getGameDataUrl() . "/data/wow/connected-realm/{$realm_id}?namespace={$this->getNamespace('dynamic')}";

        $request = $this->getAuthenticatedRequest(self::METHOD_GET, $url, $token);
        return $this->getParsedResponse($request);
    }

    /**
     * Mythic Keystone API - Returns an index of Keystone affixes.
     * @param AccessToken $token
     * @return array
     * @throws \League\OAuth2\Client\Provider\Exception\IdentityProviderException
     */
    public function getKeystoneAffixIndex(AccessToken $token) {
        $url = $this->getGameDataUrl() . "/data/wow/keystone-affix/index?namespace={$this->getNamespace('static')}";

        $request = $this->getAuthenticatedRequest(self::METHOD_GET, $url, $token);
        return $this->getParsedResponse($request);
    }

    /**
     * Mythic Keystone API - Returns a single Keystone affix by slug or ID.
     * @param AccessToken $token
     * @param mixed $keystone_affix_id
     * @return array
     * @throws \League\OAuth2\Client\Provider\Exception\IdentityProviderException
     */
    public function getKeystoneAffix(AccessToken $token, $keystone_affix_id) {
        $url = $this->getGameDataUrl() .
            "/data/wow/keystone-affix/{$keystone_affix_id}?namespace={$this->getNamespace('static')}";

        $request = $this->getAuthenticatedRequest(self::METHOD_GET, $url, $token);
        return $this->getParsedResponse($request);
    }

    /**
     * Mythic Raid Leaderboard API - Returns the leaderboard for a given raid and faction.
     * @param AccessToken $token
     * @param $raid - ie. 'uldir'
     * @param $faction - valid values are "alliance" or "horde"
     * @return array
     * @throws \League\OAuth2\Client\Provider\Exception\IdentityProviderException
     */
    public function getMythicLeaderboard(AccessToken $token, $raid, $faction) {
        $url = $this->getGameDataUrl() .
            "/data/wow/leaderboard/hall-of-fame/{$raid}/{$faction}?namespace={$this->getNamespace('dynamic')}";

        $request = $this->getAuthenticatedRequest(self::METHOD_GET, $url, $token);
        return $this->getParsedResponse($request);
    }

    /**
     * Mythic Keystone Dungeon API - Returns an index of Mythic Keystone dungeons.
     * @param AccessToken $token
     * @return array
     * @throws \League\OAuth2\Client\Provider\Exception\IdentityProviderException
     */
    public function getMythicKeystoneDungeonIndex(AccessToken $token) {
        $url = $this->getGameDataUrl() .
            "/data/wow/mythic-keystone/dungeon/index?namespace={$this->getNamespace('dynamic')}";

        $request = $this->getAuthenticatedRequest(self::METHOD_GET, $url, $token);
        return $this->getParsedResponse($request);
    }

    /**
     * Mythic Keystone Dungeon API - Returns a Mythic Keystone dungeon by ID.
     * @param AccessToken $token
     * @param integer $dungeonId
     * @return array
     * @throws \League\OAuth2\Client\Provider\Exception\IdentityProviderException
     */
    public function getMythicKeystoneDungeon(AccessToken $token, $dungeonId) {
        $url = $this->getGameDataUrl() .
            "/data/wow/mythic-keystone/dungeon/{$dungeonId}?namespace={$this->getNamespace('dynamic')}";

        $request = $this->getAuthenticatedRequest(self::METHOD_GET, $url, $token);
        return $this->getParsedResponse($request);
    }

    /**
     * Mythic Keystone Dungeon API - Returns an index of links to other documents related to Mythic Keystone dungeons.
     * @param AccessToken $token
     * @return array
     * @throws \League\OAuth2\Client\Provider\Exception\IdentityProviderException
     */
    public function getMythicKeystoneIndex(AccessToken $token) {
        $url = $this->getGameDataUrl() .
            "/data/wow/mythic-keystone/index?namespace={$this->getNamespace('dynamic')}";

        $request = $this->getAuthenticatedRequest(self::METHOD_GET, $url, $token);
        return $this->getParsedResponse($request);
    }

    /**
     * Mythic Keystone Dungeon API - Returns an index of Mythic Keystone periods.
     * @param AccessToken $token
     * @return array
     * @throws \League\OAuth2\Client\Provider\Exception\IdentityProviderException
     */
    public function getMythicKeystonePeriodIndex(AccessToken $token) {
        $url = $this->getGameDataUrl() .
            "/data/wow/mythic-keystone/period/index?namespace={$this->getNamespace('dynamic')}";

        $request = $this->getAuthenticatedRequest(self::METHOD_GET, $url, $token);
        return $this->getParsedResponse($request);
    }

    /**
     * Mythic Keystone Dungeon API - Returns a Mythic Keystone period by ID.
     * @param AccessToken $token
     * @param integer $periodId
     * @return array
     * @throws \League\OAuth2\Client\Provider\Exception\IdentityProviderException
     */
    public function getMythicKeystonePeriod(AccessToken $token, $periodId) {
        $url = $this->getGameDataUrl() .
            "/data/wow/mythic-keystone/period/{$periodId}?namespace={$this->getNamespace('dynamic')}";

        $request = $this->getAuthenticatedRequest(self::METHOD_GET, $url, $token);
        return $this->getParsedResponse($request);
    }

    /**
     * Mythic Keystone Dungeon API - Returns an index of Mythic Keystone seasons.
     * @param AccessToken $token
     * @return array
     * @throws \League\OAuth2\Client\Provider\Exception\IdentityProviderException
     */
    public function getMythicKeystoneSeasonIndex(AccessToken $token) {
        $url = $this->getGameDataUrl() .
            "/data/wow/mythic-keystone/season/index?namespace={$this->getNamespace('dynamic')}";

        $request = $this->getAuthenticatedRequest(self::METHOD_GET, $url, $token);
        return $this->getParsedResponse($request);
    }

    /**
     * Mythic Keystone Dungeon API - Returns a Mythic Keystone season by ID.
     * @param AccessToken $token
     * @param integer $_id
     * @return array
     * @throws \League\OAuth2\Client\Provider\Exception\IdentityProviderException
     */
    public function getMythicKeystoneSeason(AccessToken $token, $seasonId) {
        $url = $this->getGameDataUrl() .
            "/data/wow/mythic-keystone/season/{$seasonId}?namespace={$this->getNamespace('dynamic')}";

        $request = $this->getAuthenticatedRequest(self::METHOD_GET, $url, $token);
        return $this->getParsedResponse($request);
    }

    /**
     * Mythic Keystone Leaderboard API - Returns an index of Mythic Keystone Leaderboard dungeon instances for a connected realm.
     * @param AccessToken $token
     * @param integer $connectedRealmId
     * @return array
     * @throws \League\OAuth2\Client\Provider\Exception\IdentityProviderException
     */
    public function getMythicKeystoneLeaderboardIndex(AccessToken $token, $connectedRealmId) {
        $url = $this->getGameDataUrl() .
            "/data/wow/connected-realm/{$connectedRealmId}/mythic-leaderboard/index?namespace={$this->getNamespace('dynamic')}";

        $request = $this->getAuthenticatedRequest(self::METHOD_GET, $url, $token);
        return $this->getParsedResponse($request);
    }

    /**
     * Mythic Keystone Leaderboard API - Returns a weekly Mythic Keystone Leaderboard by period.
     * @param AccessToken $token
     * @param integer $connectedRealmId
     * @param integer $dungeonId
     * @param $period
     * @return array
     * @throws \League\OAuth2\Client\Provider\Exception\IdentityProviderException
     */
    public function getMythicKeystoneLeaderboard(AccessToken $token, $connectedRealmId, $dungeonId, $period) {
        $url = $this->getGameDataUrl() .
            "/data/wow/connected-realm/{$connectedRealmId}/mythic-leaderboard/{$dungeonId}/period/{$period}".
            "?namespace={$this->getNamespace('dynamic')}";

        $request = $this->getAuthenticatedRequest(self::METHOD_GET, $url, $token);
        return $this->getParsedResponse($request);
    }

    /**
     * Playable Class API - Returns an index of playable classes.
     * @param AccessToken $token
     * @return array
     * @throws \League\OAuth2\Client\Provider\Exception\IdentityProviderException
     */
    public function getPlayableClassesIndex(AccessToken $token) {
        $url = $this->getGameDataUrl() .
            "/data/wow/playable-class/index?namespace={$this->getNamespace('static')}";

        $request = $this->getAuthenticatedRequest(self::METHOD_GET, $url, $token);
        return $this->getParsedResponse($request);
    }

    /**
     * Playable Class API - Returns a playable class by ID.
     * @param AccessToken $token
     * @param integer $classId
     * @return array
     * @throws \League\OAuth2\Client\Provider\Exception\IdentityProviderException
     */
    public function getPlayableClass(AccessToken $token, $classId) {
        $url = $this->getGameDataUrl() .
            "/data/wow/playable-class/{$classId}?namespace={$this->getNamespace('static')}";

        $request = $this->getAuthenticatedRequest(self::METHOD_GET, $url, $token);
        return $this->getParsedResponse($request);
    }

    /**
     * **UNDOCUMENTED RESOURCE**
     * Getting a class from getPlayableClass() endpoint returns an extra resource 'media'
     * This resource returns an 'asset' array with media content and their respective URLs.
     * @param AccessToken $token
     * @param integer $mediaId
     * @return array
     * @throws \League\OAuth2\Client\Provider\Exception\IdentityProviderException
     */
    public function getPlayableClassMedia(AccessToken $token, $mediaId) {
        $url = $this->getGameDataUrl() .
            "/data/wow/media/playable-class/{$mediaId}?namespace={$this->getNamespace('static')}";

        $request = $this->getAuthenticatedRequest(self::METHOD_GET, $url, $token);
        return $this->getParsedResponse($request);
    }

    /**
     * Playable Class API - Returns the PvP talent slots for a playable class by ID.
     * @param AccessToken $token
     * @param integer $classId
     * @return array
     * @throws \League\OAuth2\Client\Provider\Exception\IdentityProviderException
     */
    public function getPlayableClassPvpTalentSlots(AccessToken $token, $classId) {
        $url = $this->getGameDataUrl() .
            "/data/wow/playable-class/{$classId}/pvp-talent-slots?namespace={$this->getNamespace('static')}";

        $request = $this->getAuthenticatedRequest(self::METHOD_GET, $url, $token);
        return $this->getParsedResponse($request);
    }

    /**
     * Playable Specialization API - Returns an index of playable specializations.
     * @param AccessToken $token
     * @return array
     * @throws \League\OAuth2\Client\Provider\Exception\IdentityProviderException
     */
    public function getPlayableSpecializationIndex(AccessToken $token) {
        $url = $this->getGameDataUrl() .
            "/data/wow/playable-specialization/index?namespace={$this->getNamespace('static')}";

        $request = $this->getAuthenticatedRequest(self::METHOD_GET, $url, $token);
        return $this->getParsedResponse($request);
    }

    /**
     * Playable Specialization API - Returns a playable specialization by ID.
     * @param AccessToken $token
     * @param integer $specId
     * @return array
     * @throws \League\OAuth2\Client\Provider\Exception\IdentityProviderException
     */
    public function getPlayableSpecialization(AccessToken $token, $specId) {
        $url = $this->getGameDataUrl() .
            "/data/wow/playable-specialization/{$specId}?namespace={$this->getNamespace('static')}";

        $request = $this->getAuthenticatedRequest(self::METHOD_GET, $url, $token);
        return $this->getParsedResponse($request);
    }

    /**
     * **UNDOCUMENTED RESOURCE**
     * Getting a spec from getPlayableSpecialization() endpoint returns an extra resource 'media'
     * This resource returns an 'asset' array with meta data
     * @param AccessToken $token
     * @param integer $mediaId
     * @return array
     * @throws \League\OAuth2\Client\Provider\Exception\IdentityProviderException
     */
    public function getPlayableSpecializationMedia(AccessToken $token, $mediaId) {
        $url = $this->getGameDataUrl() .
            "/data/wow/media/playable-specialization/{$mediaId}?namespace={$this->getNamespace('static')}";

        $request = $this->getAuthenticatedRequest(self::METHOD_GET, $url, $token);
        return $this->getParsedResponse($request);
    }

    /**
     * Power Type API - Returns an index of power types.
     * @param AccessToken $token
     * @return array
     * @throws \League\OAuth2\Client\Provider\Exception\IdentityProviderException
     */
    public function getPowerTypesIndex(AccessToken $token) {
        $url = $this->getGameDataUrl() .
            "/data/wow/power-type/index?namespace={$this->getNamespace('static')}";

        $request = $this->getAuthenticatedRequest(self::METHOD_GET, $url, $token);
        return $this->getParsedResponse($request);
    }

    /**
     * Power Type API - Returns a power type by ID.
     * @param AccessToken $token
     * @param integer $powerTypeId
     * @return array
     * @throws \League\OAuth2\Client\Provider\Exception\IdentityProviderException
     */
    public function getPowerType(AccessToken $token, $powerTypeId) {
        $url = $this->getGameDataUrl() .
            "/data/wow/power-type/{$powerTypeId}?namespace={$this->getNamespace('static')}";

        $request = $this->getAuthenticatedRequest(self::METHOD_GET, $url, $token);
        return $this->getParsedResponse($request);
    }

    /**
     * Playable Race API - Returns an index of races.
     * @param AccessToken $token
     * @return array
     * @throws \League\OAuth2\Client\Provider\Exception\IdentityProviderException
     */
    public function getRacesIndex(AccessToken $token) {
        $url = $this->getGameDataUrl() .
            "/data/wow/race/index?namespace={$this->getNamespace('static')}";

        $request = $this->getAuthenticatedRequest(self::METHOD_GET, $url, $token);
        return $this->getParsedResponse($request);
    }

    /**
     * Playable Race API - Returns a race by ID.
     * @param AccessToken $token
     * @param integer $raceId
     * @return array
     * @throws \League\OAuth2\Client\Provider\Exception\IdentityProviderException
     */
    public function getRace(AccessToken $token, $raceId) {
        $url = $this->getGameDataUrl() .
            "/data/wow/race/{$raceId}?namespace={$this->getNamespace('static')}";

        $request = $this->getAuthenticatedRequest(self::METHOD_GET, $url, $token);
        return $this->getParsedResponse($request);
    }

    /**
     * Realm API - Returns an index of realms.
     * @param AccessToken $token
     * @param integer $_id
     * @return array
     * @throws \League\OAuth2\Client\Provider\Exception\IdentityProviderException
     */
    public function getRealmIndex(AccessToken $token, $_id) {
        $url = $this->getGameDataUrl() .
            "/data/wow/realm/index?namespace={$this->getNamespace('dynamic')}";

        $request = $this->getAuthenticatedRequest(self::METHOD_GET, $url, $token);
        return $this->getParsedResponse($request);
    }

    /**
     * Realm API - Returns a single realm by slug or ID.
     * @param AccessToken $token
     * @param mixed $realm
     * @return array
     * @throws \League\OAuth2\Client\Provider\Exception\IdentityProviderException
     */
    public function getRealm(AccessToken $token, $realm) {
        $url = $this->getGameDataUrl() .
            "/data/wow/realm/{$realm}?namespace={$this->getNamespace('dynamic')}";

        $request = $this->getAuthenticatedRequest(self::METHOD_GET, $url, $token);
        return $this->getParsedResponse($request);
    }

    /**
     * Region API - Returns an index of regions.
     * @param AccessToken $token
     * @param integer $_id
     * @return array
     * @throws \League\OAuth2\Client\Provider\Exception\IdentityProviderException
     */
    public function getRegionIndex(AccessToken $token, $_id) {
        $url = $this->getGameDataUrl() .
            "/data/wow/region/index?namespace={$this->getNamespace('dynamic')}";

        $request = $this->getAuthenticatedRequest(self::METHOD_GET, $url, $token);
        return $this->getParsedResponse($request);
    }

    /**
     * Region API - Returns a single region by ID.
     * @param AccessToken $token
     * @param integer $regionId
     * @return array
     * @throws \League\OAuth2\Client\Provider\Exception\IdentityProviderException
     */
    public function getRegion(AccessToken $token, $regionId) {
        $url = $this->getGameDataUrl() .
            "/data/wow/region/{$regionId}?namespace={$this->getNamespace('dynamic')}";

        $request = $this->getAuthenticatedRequest(self::METHOD_GET, $url, $token);
        return $this->getParsedResponse($request);
    }

    /**
     * WoW Token API - Returns the WoW Token index.
     * @param AccessToken $token
     * @return array
     * @throws \League\OAuth2\Client\Provider\Exception\IdentityProviderException
     */
    public function getTokenIndex(AccessToken $token) {
        $url = $this->getGameDataUrl() .
            "/data/wow/token/index?namespace={$this->getNamespace('dynamic')}";

        $request = $this->getAuthenticatedRequest(self::METHOD_GET, $url, $token);
        return $this->getParsedResponse($request);
    }

    /********************/
    /*   Profile APIs   *
    /********************/

    /**
     * WoW Mythic Keystone Character Profile API - Returns a Mythic Keystone Profile index for a character.
     * @param AccessToken $token
     * @param string $realmSlug
     * @param string $characterName
     * @return array
     * @throws \League\OAuth2\Client\Provider\Exception\IdentityProviderException
     */
    public function getCharacterMythicKeystoneProfile(AccessToken $token, $realmSlug, $characterName) {
        $url = $this->getGameDataUrl() .
            "/profile/wow/character/{$realmSlug}/{$characterName}/mythic-keystone-profile".
            "?namespace={$this->getNamespace('profile')}";

        $request = $this->getAuthenticatedRequest(self::METHOD_GET, $url, $token);
        return $this->getParsedResponse($request);
    }

    /**
     * WoW Token API - Returns the WoW Token index.
     * @param AccessToken $token
     * @param string $realmSlug
     * @param string $characterName
     * @param integer $seasonId
     * @return array
     * @throws \League\OAuth2\Client\Provider\Exception\IdentityProviderException
     */
    public function getCharacterMythicKeystoneProfileSeason(AccessToken $token, $realmSlug, $characterName, $seasonId) {
        $url = $this->getGameDataUrl() .
            "/profile/wow/character/{$realmSlug}/{$characterName}/mythic-keystone-profile/season/{$seasonId}".
            "?namespace={$this->getNamespace('profile')}";

        $request = $this->getAuthenticatedRequest(self::METHOD_GET, $url, $token);
        return $this->getParsedResponse($request);
    }
}
