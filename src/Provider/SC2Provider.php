<?php

namespace Depotwarehouse\OAuth2\Client\Provider;

use Depotwarehouse\OAuth2\Client\Entity\SC2User;
use League\OAuth2\Client\Token\AccessToken;

/* @TODO: Double check everything SC2 */
class SC2Provider extends BattleNet
{

    protected $game = "sc2";

    /**
     * Return an access token for a Client ID + Client Secret combination
     *
     * @param array $options
     *
     * @return \League\OAuth2\Client\Token\AccessTokenInterface
     * @throws \League\OAuth2\Client\Provider\Exception\IdentityProviderException
     */
    public function getClientAccessToken(array $options = []) {
        return parent::getAccessToken('client_credentials', $options);
    }

    /**
     * @param AccessToken $token
     * @return string
     */
    public function getResourceOwnerDetailsUrl(AccessToken $token)
    {
        return "https://{$this->region}.api.battle.net/sc2/profile/user?access_token={$token}";
    }

    protected function createResourceOwner(array $response, AccessToken $token)
    {
        $response = (array)($response['characters'][0]);

        $user = new SC2User($response, $this->region);

        return $user;
    }


}
