<?php

namespace Depotwarehouse\OAuth2\Client\Entity;

use League\OAuth2\Client\Provider\ResourceOwnerInterface;

class BattleNetUser implements ResourceOwnerInterface {

    public $id;
    public $battletag;

    public function __construct(array $attributes, $region)
    {
        $this->id = $attributes['id'];
        $this->battletag = $attributes['battletag'];
    }

    public function toArray()
    {
        return [
            'id' => $this->id,
            'battletag' => $this->realm,
        ];
    }
    public function getId()
    {
        return $this->id;
    }
}
