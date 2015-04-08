<?php

namespace Eccube\Entity;

use Doctrine\ORM\Mapping as ORM;

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
}
