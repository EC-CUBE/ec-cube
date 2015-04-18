<?php

namespace Eccube\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * DelivTime
 */
class DelivTime extends \Eccube\Entity\AbstractEntity
{
    /**
     * @var integer
     */
    private $deliv_id;

    /**
     * @var integer
     */
    private $time_id;

    /**
     * @var string
     */
    private $deliv_time;

    /**
     * @var \Eccube\Entity\Deliv
     */
    private $Deliv;


    /**
     * Set deliv_id
     *
     * @param integer $delivId
     * @return DelivTime
     */
    public function setDelivId($delivId)
    {
        $this->deliv_id = $delivId;

        return $this;
    }

    /**
     * Get deliv_id
     *
     * @return integer 
     */
    public function getDelivId()
    {
        return $this->deliv_id;
    }

    /**
     * Set time_id
     *
     * @param integer $timeId
     * @return DelivTime
     */
    public function setTimeId($timeId)
    {
        $this->time_id = $timeId;

        return $this;
    }

    /**
     * Get time_id
     *
     * @return integer 
     */
    public function getTimeId()
    {
        return $this->time_id;
    }

    /**
     * Set deliv_time
     *
     * @param string $delivTime
     * @return DelivTime
     */
    public function setDelivTime($delivTime)
    {
        $this->deliv_time = $delivTime;

        return $this;
    }

    /**
     * Get deliv_time
     *
     * @return string 
     */
    public function getDelivTime()
    {
        return $this->deliv_time;
    }

    /**
     * Set Deliv
     *
     * @param \Eccube\Entity\Deliv $deliv
     * @return DelivTime
     */
    public function setDeliv(\Eccube\Entity\Deliv $deliv = null)
    {
        $this->Deliv = $deliv;

        return $this;
    }

    /**
     * Get Deliv
     *
     * @return \Eccube\Entity\Deliv 
     */
    public function getDeliv()
    {
        return $this->Deliv;
    }
}
