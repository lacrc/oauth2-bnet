<?php

namespace Depotwarehouse\OAuth2\Client\Provider;

use Depotwarehouse\OAuth2\Client\Entity\WowUser;
use League\OAuth2\Client\Token\AccessToken;

class WowProvider extends BattleNet {

    /**
     * @var string
     */
    protected $game = "wow";

    protected function getBaseEndpoint() {
        switch ($this->region) {
            case 'cn':
                return "https://gateway.battlenet.com.cn";
            default:
                return "https://{$this->region}.api.blizzard.com";
        }
    }

    public function getCharactersArray(AccessToken $token) {
        $url = $this->getBaseEndpoint()."/wow/user/characters";

        $request = $this->getAuthenticatedRequest(self::METHOD_GET, $url, $token);

        return $this->getResponse($request);
    }
}
