<?php

namespace Eccube\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Delivtime
 */
class Delivtime extends \Eccube\Entity\AbstractEntity
{
    /**
     * @var integer
     */
    private $delivId;

    /**
     * @var integer
     */
    private $timeId;

    /**
     * @var string
     */
    private $delivTime;


    /**
     * Set delivId
     *
     * @param integer $delivId
     * @return Delivtime
     */
    public function setDelivId($delivId)
    {
        $this->delivId = $delivId;

        return $this;
    }

    /**
     * Get delivId
     *
     * @return integer 
     */
    public function getDelivId()
    {
        return $this->delivId;
    }

    /**
     * Set timeId
     *
     * @param integer $timeId
     * @return Delivtime
     */
    public function setTimeId($timeId)
    {
        $this->timeId = $timeId;

        return $this;
    }

    /**
     * Get timeId
     *
     * @return integer 
     */
    public function getTimeId()
    {
        return $this->timeId;
    }

    /**
     * Set delivTime
     *
     * @param string $delivTime
     * @return Delivtime
     */
    public function setDelivTime($delivTime)
    {
        $this->delivTime = $delivTime;

        return $this;
    }

    /**
     * Get delivTime
     *
     * @return string 
     */
    public function getDelivTime()
    {
        return $this->delivTime;
    }
}
