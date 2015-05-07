<?php

namespace Eccube\Entity;

/**
 * OwnersstoreSetting
 */
class OwnersstoreSetting extends \Eccube\Entity\AbstractEntity
{
    /**
     * @var string
     */
    private $public_key;

    /**
     * Get public_key
     *
     * @return string
     */
    public function getPublicKey()
    {
        return $this->public_key;
    }

    /**
     * Set public_key
     *
     * @param  string             $publicKey
     * @return OwnersstoreSetting
     */
    public function setPublicKey($publicKey)
    {
        $this->public_key = $publicKey;

        return $this;
    }
}
