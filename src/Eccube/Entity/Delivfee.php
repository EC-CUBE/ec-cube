<?php

namespace Eccube\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Delivfee
 */
class Delivfee extends \Eccube\Entity\AbstractEntity
{
    /**
     * @var integer
     */
    private $delivId;

    /**
     * @var integer
     */
    private $feeId;

    /**
     * @var string
     */
    private $fee;

    /**
     * @var integer
     */
    private $pref;


    /**
     * Set delivId
     *
     * @param integer $delivId
     * @return Delivfee
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
     * Set feeId
     *
     * @param integer $feeId
     * @return Delivfee
     */
    public function setFeeId($feeId)
    {
        $this->feeId = $feeId;

        return $this;
    }

    /**
     * Get feeId
     *
     * @return integer 
     */
    public function getFeeId()
    {
        return $this->feeId;
    }

    /**
     * Set fee
     *
     * @param string $fee
     * @return Delivfee
     */
    public function setFee($fee)
    {
        $this->fee = $fee;

        return $this;
    }

    /**
     * Get fee
     *
     * @return string 
     */
    public function getFee()
    {
        return $this->fee;
    }

    /**
     * Set pref
     *
     * @param integer $pref
     * @return Delivfee
     */
    public function setPref($pref)
    {
        $this->pref = $pref;

        return $this;
    }

    /**
     * Get pref
     *
     * @return integer 
     */
    public function getPref()
    {
        return $this->pref;
    }
}
